<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\ErrorLogs\DeleteErrorLogAction;
use App\Actions\ErrorLogs\ListErrorLogOccurrencesAction;
use App\Actions\ErrorLogs\ListErrorLogsAction;
use App\Actions\ErrorLogs\SummarizeErrorLogOccurrencesAction;
use App\Actions\ErrorLogs\SummarizeErrorLogsAction;
use App\Actions\ErrorLogs\UpdateErrorLogResolutionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ErrorLogs\DestroyErrorLogRequest;
use App\Http\Requests\Admin\ErrorLogs\IndexErrorLogRequest;
use App\Http\Requests\Admin\ErrorLogs\ShowErrorLogRequest;
use App\Http\Requests\Admin\ErrorLogs\TriggerTestErrorRequest;
use App\Http\Requests\Admin\ErrorLogs\UpdateErrorLogResolutionRequest;
use App\Http\Resources\ErrorLogDetailResource;
use App\Http\Resources\ErrorLogResource;
use App\Models\ErrorLog;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ErrorLogController extends Controller
{
    public function index(
        IndexErrorLogRequest $request,
        ListErrorLogsAction $listErrorLogs,
        SummarizeErrorLogsAction $summarizeErrorLogs,
    ): Response {
        return Inertia::render('admin/error-logs/Index', [
            'errorLogs' => ErrorLogResource::collection($listErrorLogs->handle(
                $request->search(),
                $request->resolution(),
                $request->statusCodeRange(),
                $request->sortOrder(),
            )),
            'summary' => $summarizeErrorLogs->handle(),
            'filters' => [
                'search' => $request->search() ?? '',
                'resolution' => $request->resolution()->value,
                'status' => $request->statusCodeRange()->value ?? '',
                'sort' => $request->sortOrder()->value,
            ],
        ]);
    }

    public function show(
        ShowErrorLogRequest $request,
        ErrorLog $errorLog,
        ListErrorLogOccurrencesAction $listOccurrences,
        SummarizeErrorLogOccurrencesAction $summarizeOccurrences,
    ): Response {
        $errorLog->load('user:id,name,email,avatar');

        return Inertia::render('admin/error-logs/Show', [
            'errorLog' => ErrorLogDetailResource::make($errorLog)->resolve($request),
            'occurrences' => ErrorLogResource::collection($listOccurrences->handle($errorLog)),
            'occurrenceSummary' => $summarizeOccurrences->handle($errorLog),
        ]);
    }

    public function updateResolution(
        UpdateErrorLogResolutionRequest $request,
        ErrorLog $errorLog,
        UpdateErrorLogResolutionAction $updateResolution,
    ): RedirectResponse {
        $updateResolution->handle($errorLog, $request->isResolved());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $request->isResolved() ? __('messages.error_log.resolved') : __('messages.error_log.reopened'),
        ]);

        return back();
    }

    public function destroy(
        DestroyErrorLogRequest $request,
        ErrorLog $errorLog,
        DeleteErrorLogAction $deleteErrorLog,
    ): RedirectResponse {
        $deleteErrorLog->handle($errorLog);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('messages.error_log.deleted')]);

        return to_route('admin.error-logs.index');
    }

    public function triggerTestError(TriggerTestErrorRequest $request): never
    {
        throw new RuntimeException('Intentional test error triggered from the admin panel.');
    }
}
