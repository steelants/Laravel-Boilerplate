<?php

namespace SteelAnts\LaravelBoilerplate\Support\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use SteelAnts\DataTable\Support\BulkResult;
use SteelAnts\LaravelBoilerplate\Exceptions\ActionException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Vyhodnocuje a spouští akce ze sady. Viditelnost (stav objektu + ability) se kontroluje stejnou metodou
 * při vykreslení i při spuštění, takže akci, kterou uživatel nevidí, nejde ani zavolat z prohlížeče.
 */
final class ActionRunner
{
    public function __construct(
        private readonly Container $container,
        private readonly Gate $gate,
    ) {
    }

    /** @return list<ActionItem> akce, které uživatel nad objektem vidí (bez unlisted) */
    public function items(ActionSet $set, object $subject): array
    {
        $items = [];
        foreach ($set->actions() as $action) {
            if ($action->isListed() && $this->allows($action, $subject)) {
                $items[] = new ActionItem(
                    name: $action->name,
                    text: $action->labelFor($subject),
                    icon: $action->iconFor($subject),
                    url: $action->urlFor($subject),
                    confirm: $action->confirmFor($subject),
                    destructive: $action->isDestructive(),
                    method: $action->method(),
                    parameters: $action->isLivewire() ? ($action->parametersFor($subject) ?? $set->key($subject)) : null,
                );
            }
        }

        return $items;
    }

    public function allows(Action $action, object $subject): bool
    {
        return $action->isVisibleFor($subject)
            && ($action->ability() === null || $this->gate->allows($action->ability(), $subject));
    }

    /**
     * @throws NotFoundHttpException neznámá akce, odkaz nebo Livewire akce (nemá obsluhu)
     * @throws AuthorizationException akce není pro uživatele / stav objektu dostupná
     */
    public function run(ActionSet $set, object $subject, string $name): ActionResult
    {
        $action = $set->find($name);

        if ($action === null || $action->handler() === null) {
            throw new NotFoundHttpException();
        }

        if (!$this->allows($action, $subject)) {
            throw new AuthorizationException();
        }

        $result = $this->container->call($action->handler(), Action::parameters($subject));

        return $result instanceof ActionResult ? $result : ActionResult::refresh();
    }

    /** @return list<ActionItem> hromadné akce, které se uživateli nabídnou (rozhoduje gate bez objektu) */
    public function bulkItems(BulkActionSet $set): array
    {
        $items = [];
        foreach ($set->actions() as $action) {
            if ($this->allowsBulk($action)) {
                $items[] = new ActionItem(
                    name: $action->name,
                    text: $action->labelText(),
                    icon: $action->iconText(),
                    confirm: $action->confirmText(),
                    destructive: $action->isDestructive(),
                );
            }
        }

        return $items;
    }

    public function allowsBulk(BulkAction $action): bool
    {
        return $action->gate() === null || $this->gate->allows($action->gate());
    }

    /**
     * Každý objekt výběru projde applicable() a can() zvlášť; neprošlé se přeskočí s důvodem.
     * Výjimka obsluhy each() označí objekt jako selhaný (report) a pokračuje se dalším.
     *
     * @param  list<string>  $keys
     *
     * @throws NotFoundHttpException neznámá hromadná akce
     * @throws AuthorizationException akce se uživateli nenabízí (gate)
     */
    public function runBulk(BulkActionSet $set, array $keys, string $name): BulkResult
    {
        $action = $set->find($name) ?? throw new NotFoundHttpException();

        if (!$this->allowsBulk($action)) {
            throw new AuthorizationException();
        }

        if ($action->keysHandler() !== null) {
            $result = $this->container->call($action->keysHandler(), ['keys' => $keys]);

            return $result instanceof BulkResult ? $result : new BulkResult(is_int($result) ? $result : count($keys));
        }

        $ok = $failed = 0;
        $reasons = [];
        $passed = [];

        foreach ($set->subjects($keys) as $subject) {
            $reason = $this->bulkSkipReason($action, $subject);

            if ($reason === null && $action->handler() !== null) {
                $passed[] = $subject;

                continue;
            }

            if ($reason === null) {
                try {
                    $this->container->call($action->eachHandler(), Action::parameters($subject));
                    $ok++;

                    continue;
                } catch (AuthorizationException) {
                    $reason = __('You are not authorized to perform this action.');
                } catch (ActionException $e) {
                    $reason = $e->getMessage();
                } catch (Throwable $e) {
                    report($e);
                    $failed++;

                    continue;
                }
            }

            $reasons[$reason] = ($reasons[$reason] ?? 0) + 1;
        }

        if ($passed !== []) {
            $subjects = collect($passed);
            $result = $this->container->call($action->handler(), ['subjects' => $subjects, Collection::class => $subjects]);

            if (!$result instanceof BulkResult) {
                return new BulkResult(count($passed), array_sum($reasons), 0, $reasons);
            }

            $skipped = array_sum($reasons) + $result->skipped;
            foreach ($result->reasons as $reason => $count) {
                $reasons[$reason] = ($reasons[$reason] ?? 0) + $count;
            }

            return new BulkResult($result->ok, $skipped, $result->failed, $reasons);
        }

        return new BulkResult($ok, array_sum($reasons), $failed, $reasons);
    }

    /** Důvod, proč se objekt výběru přeskočí (stav objektu, chybějící ability), nebo null. */
    private function bulkSkipReason(BulkAction $action, object $subject): ?string
    {
        if (($reason = $action->notApplicableReason($subject)) !== null) {
            return $reason;
        }

        return $action->ability() !== null && $this->gate->denies($action->ability(), $subject) ? __('You are not authorized to perform this action.') : null;
    }
}
