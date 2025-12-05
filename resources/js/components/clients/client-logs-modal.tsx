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
import { ScrollArea } from "@/components/ui/scroll-area";
import { Badge } from "@/components/ui/badge";
import JsonPreview from "./json-preview";

interface Props {
    open: boolean;
    onClose: () => void;
    logs: App.DTO.Client.ClientLogDto[] | null;
}

export default function ClientLogsModal({ open, onClose, logs }: Props) {
    const rowClass =
        "odd:bg-muted/50 odd:hover:bg-muted/50 border-none hover:bg-transparent";

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="!max-w-6xl max-h-[90vh] overflow-hidden">
                <DialogHeader>
                    <DialogTitle>Client Logs</DialogTitle>
                </DialogHeader>

                <div className="max-h-[70vh] overflow-auto">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead>Id</TableHead>
                                <TableHead>Webhook Event</TableHead>
                                <TableHead>Task Status</TableHead>
                                <TableHead>Worker Id</TableHead>
                                <TableHead>Finished At</TableHead>
                                <TableHead>Result</TableHead>
                                <TableHead>Webhook Data</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {logs?.map((log) => (
                                <TableRow key={log.id} className={rowClass}>
                                    <TableCell>{log.id}</TableCell>
                                    <TableCell>{log.webhook_event}</TableCell>
                                    <TableCell>
                                        {log.task_status ? (
                                            <Badge variant="secondary">
                                                {log.task_status}
                                            </Badge>
                                        ) : (
                                            <span className="text-muted-foreground italic">–</span>
                                        )}
                                    </TableCell>
                                    <TableCell>{log.worker_id ?? <span className="text-muted-foreground italic">–</span>}</TableCell>
                                    <TableCell>{log.finished_at ?? <span className="text-muted-foreground italic">–</span>}</TableCell>
                                    <TableCell>{log.result ?? <span className="text-muted-foreground italic">–</span>}</TableCell>
                                    <TableCell>
                                        <JsonPreview data={log.webhook_data} />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </DialogContent>
        </Dialog>
    );
}
