"use client";

import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import InputSearch from "@/components/ui/input-search";
import { CrudTable } from "@/components/common/crud-table";
import { useCrudTableState } from "@/hooks/use-crud-table-state";
import useInertiaUrlState from "@/hooks/use-inertia-url-state";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import React, { useEffect } from "react";
import { BarChart3, MessageSquare, Play, PlayCircle, RefreshCw } from "lucide-react";

import AnalysisModal from "@/components/admin-panel/analysis-dialog";
import PayloadModal from "@/components/admin-panel/payload-dialog";
import { confirm } from "@/components/ui/confirmer";


import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";
import PromptRetentionDialog from "@/components/admin-panel/prompt-retention-dialog";
import { toast } from "sonner";

interface Props {
    reports: App.DTO.PaginatedListDto<App.DTO.Operator.AiRetentionReportListDto>;
    prompt: string | null;
}

export default function AiRetentions({ reports, prompt }: Props) {
    const api = useApi();

    const [currentPrompt, setCurrentPrompt] = React.useState<string>(prompt ?? "");
    const [isSavingPrompt, setIsSavingPrompt] = React.useState(false);
    const [isTestingPrompt, setIsTestingPrompt] = React.useState(false);
    const [isProcessing, setIsProcessing] = React.useState(false);

    const [autoRefresh, setAutoRefresh] = React.useState(false);


    const handleSavePrompt = React.useCallback(async () => {
        setIsSavingPrompt(true);
        await api.put(
            route("admin-panel.ai-reports.edit-prompt"),
            { prompt: currentPrompt },
            {
                onSuccess: () => setIsSavingPrompt(false),
                onError: () => setIsSavingPrompt(false),
            }
        );
    }, [api, currentPrompt]);

    const handleTestPrompt = React.useCallback(async (): Promise<void> => {
        setIsTestingPrompt(true);
        await api.get(route("admin-panel.ai-reports.test"), {
            onSuccess: () => {
                setIsTestingPrompt(false);
                toast.success("Test started successfully");
            },
            onError: () => {
                setIsTestingPrompt(false);
                toast.error("Failed to trigger test prompt reports");
            },
        });
    }, [api]);


    const handleProcessJob = async () => {
        const confirmed = await confirm({
            title: "Are you absolutely sure?",
            description: "This action will trigger AI retention job processing. Continue?",
            actionText: "Run processing",
            cancelText: "Cancel",
        });

        if (!confirmed) return;

        setIsProcessing(true);
        await api.get(route("admin-panel.ai-reports.process-job"), undefined, {
            onSuccess: () => {
                setIsProcessing(false);
                toast.success("Processing started successfully");
            },
            onError: () => {
                setIsProcessing(false);
                toast.error("Failed to start processing");
            },
        });
    };



    // --- URL-состояние: поиск + пагинация ---
    const [filters, setFilters] = useInertiaUrlState(
        { search: "", page: 1 },
        {
            autoSubmit: true,
            omitDefaults: ["search", "page"],
            routerOptions: { preserveState: true, preserveScroll: true },
        }
    );

    // --- CrudTable state ---
    const crud = useCrudTableState<App.DTO.Operator.AiRetentionReportListDto>({
        defaultForm: () => ({}),
        initialData: reports,
    });

    // --- Модалки анализа/пейлоада ---
    const [analysisOpen, setAnalysisOpen] = React.useState(false);
    const [analysisTitle, setAnalysisTitle] = React.useState<string>("Analysis");
    const [analysisText, setAnalysisText] = React.useState<string>("");
    const [analysisScore, setAnalysisScore] = React.useState<number | null>(null); // ← для цветного бэйджа

    const [payloadOpen, setPayloadOpen] = React.useState(false);
    const [payloadTitle, setPayloadTitle] = React.useState<string>("Payload");
    const [payloadData, setPayloadData] = React.useState<unknown>(null);

    const handleSearchChange = (value: string) => setFilters({ search: value, page: 1 });
    const handlePageChange = (page: number) => setFilters({ page });

    const openAnalysis = (item: App.DTO.Operator.AiRetentionReportListDto) => {
        setAnalysisTitle(`Analysis for Report #${item.id}`);
        setAnalysisText(item.analysis || "");
        setAnalysisScore(typeof item.score === "number" ? item.score : null);
        setAnalysisOpen(true);
    };

    const openPayload = (item: App.DTO.Operator.AiRetentionReportListDto) => {
        setPayloadTitle(`Payload for Report #${item.id}`);
        setPayloadData(item.raw_payload ?? null);
        setPayloadOpen(true);
    };

    const closeAnalysis = () => setAnalysisOpen(false);
    const closePayload = () => setPayloadOpen(false);

    useEffect(() => {
        if (!autoRefresh) return;

        const interval = setInterval(() => {
            setFilters({
                search: filters.search,
                page: filters.page,
            });
        }, 5000);

        return () => clearInterval(interval);
    }, [autoRefresh, api, crud, filters.search, filters.page]);

    return (
        <AppLayout>
            <Head title="AI Retentions" />

            <div className="flex flex-col gap-6">
                <h1 className="text-2xl font-bold mb-4">AI Retentions</h1>

                <div className="flex items-center gap-2 mb-6">
                    <Button
                        type="button"
                        onClick={handleProcessJob}
                        disabled={isProcessing}
                        title="Run processing job"
                    >
                        <Play size={16} />
                        {isProcessing ? "Processing..." : "Run Processing"}
                    </Button>

                    <PromptRetentionDialog
                        prompt={currentPrompt}
                        onChange={setCurrentPrompt}
                        onSave={handleSavePrompt}
                        onTest={handleTestPrompt}
                        isSaving={isSavingPrompt}
                        isTesting={isTestingPrompt}
                    />

                    <Button
                        type="button"
                        variant={autoRefresh ? "default" : "outline"}
                        onClick={() => setAutoRefresh((prev) => !prev)}
                        title={autoRefresh ? "Disable auto-refresh" : "Enable auto-refresh"}
                    >
                        <RefreshCw
                            size={16}
                            className={autoRefresh ? "animate-spin-slow" : ""}
                        />
                        Auto Refresh
                    </Button>
                </div>

                <InputSearch
                    defaultValue={filters.search}
                    onChangeDebounced={handleSearchChange}
                    placeholder="Search by comment, analysis, client/operator/user id…"
                />

                <CrudTable<App.DTO.Operator.AiRetentionReportListDto>
                    tableTitle="AI Retention Test Reports"
                    tableDescription="List of generated AI retention test reports"
                    resourceName="report"
                    crudState={crud}
                    columns={[
                        { key: "id", title: "ID" },
                        { key: "operator_id", title: "Operator" },
                        { key: "client_id", title: "Client" },
                        {
                            key: "user",
                            title: "User",
                            render: (item) =>
                                (item as any).user?.name ? (
                                    <span className="text-muted-foreground">{(item as any).user.name}</span>
                                ) : (
                                    <span className="text-muted-foreground">–</span>
                                ),
                        },
                        {
                            key: "score",
                            title: "Score",
                            render: (item) => {
                                const score = item.score ?? null;
                                let colorClass = "";
                                if (typeof score === "number") {
                                    if (score === 1) colorClass = "!bg-red/15 text-red";
                                    else if (score >= 2 && score <= 3) colorClass = "!bg-orange/15 text-orange";
                                    else if (score >= 4) colorClass = "!bg-green/15 text-green";
                                }
                                return typeof score === "number" ? (
                                    <Badge variant="secondary" className={colorClass}>
                                        {score}
                                    </Badge>
                                ) : (
                                    <span className="text-muted-foreground">–</span>
                                );
                            },
                        },
                        {
                            key: "comment",
                            title: "Comment",
                            render: (item) =>
                                item.comment ? (
                                    <div className="max-w-[420px] whitespace-pre-wrap" title={item.comment}>
                                        {item.comment}
                                    </div>
                                ) : (
                                    <span className="text-muted-foreground">–</span>
                                ),
                        },
                        {
                            key: "conversation_date",
                            title: "Conversation Date",
                            render: (item) =>
                                item.conversation_date ? (
                                    <time dateTime={item.conversation_date}>
                                        {new Date(item.conversation_date).toLocaleDateString()}
                                    </time>
                                ) : (
                                    <span className="text-muted-foreground">–</span>
                                ),
                        },
                    ]}
                    actions={(item) => (
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="icon"
                                title="View analysis"
                                onClick={() => openAnalysis(item)}
                                disabled={!item.analysis}
                            >
                                <BarChart3 size={16} />
                            </Button>
                            <Button
                                variant="outline"
                                size="icon"
                                title="View payload"
                                onClick={() => openPayload(item)}
                                disabled={!item.raw_payload}
                            >
                                <MessageSquare size={16} />
                            </Button>
                        </div>
                    )}
                    pagination={{
                        currentPage: reports.currentPage,
                        totalPages: reports.lastPage,
                        onPageChange: handlePageChange,
                        paginationItemsToDisplay: 3,
                    }}
                />
            </div>

            {/* Модалки */}
            <AnalysisModal
                open={analysisOpen}
                onClose={closeAnalysis}
                title={analysisTitle}
                text={analysisText}
            />

            <PayloadModal
                open={payloadOpen}
                onClose={closePayload}
                title={payloadTitle}
                payload={payloadData}
            />
        </AppLayout>
    );
}
