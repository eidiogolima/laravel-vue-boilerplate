export type ErrorLogContext = 'http' | 'console';

export type ErrorLogResolution = 'open' | 'resolved' | 'all';

export type HttpStatusCodeRange = '5xx' | '4xx';

export type ErrorLogSortOrder = 'newest' | 'oldest';

export type ErrorLogListItem = {
    id: number;
    status_code: number;
    exception: string;
    message: string;
    file: string | null;
    line: number | null;
    method: string | null;
    url: string | null;
    context: ErrorLogContext;
    user_name: string | null;
    is_resolved: boolean;
    created_at: string | null;
};

export type StackFrame = {
    file: string | null;
    line: number | null;
    function: string;
    is_vendor: boolean;
};

export type CodeSnippetLine = {
    line: number;
    code: string;
};

export type ErrorLogDetail = {
    id: number;
    status_code: number;
    exception: string;
    message: string | null;
    file: string | null;
    line: number | null;
    code_snippet: CodeSnippetLine[];
    trace: StackFrame[];
    context: ErrorLogContext;
    method: string | null;
    url: string | null;
    route: string | null;
    ip: string | null;
    user_agent: string | null;
    user: {
        id: number;
        name: string;
        email: string;
        avatar: string | null;
    } | null;
    payload: Record<string, unknown>;
    headers: Record<string, string>;
    environment: string;
    resolved_at: string | null;
    created_at: string | null;
};

export type ErrorLogSummary = {
    last_24_hours: number;
    open: number;
    distinct_open: number;
    resolved: number;
    total: number;
};

export type ErrorLogOccurrenceSummary = {
    total: number;
    first_occurred_at: string | null;
    last_occurred_at: string | null;
};

export type ErrorLogFilters = {
    search: string;
    resolution: ErrorLogResolution;
    status: HttpStatusCodeRange | '';
    sort: ErrorLogSortOrder;
};
