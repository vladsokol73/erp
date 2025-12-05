"use client";

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import DialogMessages from "@/components/admin-panel/dialog-messages";

interface Props {
    open: boolean;
    onClose: () => void;
    reports: App.DTO.Operator.AiRetentionReportDto[] | null;
}

export default function OperatorReportsModal({ open, onClose, reports }: Props) {
    const rowClass = "odd:bg-muted/50 border-none hover:bg-transparent";
    const hasReports = !!reports?.length;

    const parseJsonSafe = (value: unknown): unknown | null => {
        if (value == null) return null;
        if (typeof value === "string") {
            try {
                return JSON.parse(value);
            } catch {
                return null;
            }
        }
        return value;
    };

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="!max-w-7xl max-h-[90vh] overflow-hidden">
                <DialogHeader>
                    <DialogTitle>AI Retention Reports</DialogTitle>
                </DialogHeader>

                <div className="max-h-[70vh] overflow-auto">
                    {hasReports ? (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Client ID</TableHead>
                                    <TableHead>Score</TableHead>
                                    <TableHead>Comment</TableHead>
                                    <TableHead>Analysis</TableHead>
                                    <TableHead>Dialog</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {reports!.map((report) => {
                                    const score = report.score ?? 0;
                                    let colorClass = "";
                                    if (score === 1) colorClass = "!bg-red/15 text-red";
                                    else if (score >= 2 && score <= 3) colorClass = "!bg-orange/15 text-orange";
                                    else if (score >= 4) colorClass = "!bg-green/15 text-green";

                                    const parsedPayload = parseJsonSafe(report.raw_payload);
                                    const messages: string[] =
                                        parsedPayload &&
                                        typeof parsedPayload === "object" &&
                                        "messages" in (parsedPayload as Record<string, unknown>)
                                            ? ((parsedPayload as Record<string, unknown>)
                                                .messages as string[])
                                            : [];

                                    return (
                                        <TableRow key={report.id} className={rowClass}>
                                            <TableCell>{report.client_id}</TableCell>

                                            <TableCell>
                                                <Badge variant="secondary" className={colorClass}>
                                                    {score || "–"}
                                                </Badge>
                                            </TableCell>

                                            <TableCell className="py-2 max-w-[100px]">
                                                {report.comment ? (
                                                    <p className="mt-1 whitespace-pre-wrap break-words">
                                                        {report.comment}
                                                    </p>
                                                ) : (
                                                    <p className="mt-1 text-muted-foreground italic">–</p>
                                                )}
                                            </TableCell>

                                            <TableCell className="py-2 max-w-[200px]">
                                                {report.analysis ? (
                                                    <p className="mt-1 whitespace-pre-wrap break-words">
                                                        {report.analysis}
                                                    </p>
                                                ) : (
                                                    <p className="mt-1 text-muted-foreground italic">–</p>
                                                )}
                                            </TableCell>

                                            <TableCell className="py-2">
                                                {messages.length > 0 ? (
                                                    <DialogMessages messages={messages} />
                                                ) : (
                                                    <span className="text-muted-foreground italic">–</span>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>
                    ) : (
                        <div className="flex items-center justify-center h-40 text-muted-foreground italic">
                            No AI retention reports found for this operator
                        </div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
