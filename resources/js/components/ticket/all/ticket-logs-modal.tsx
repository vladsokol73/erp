import React from "react";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Logs } from "lucide-react";

interface TicketLogsModalProps {
    logs: App.DTO.Ticket.TicketLogDto[];
}

const TicketLogsModal: React.FC<TicketLogsModalProps> = ({ logs }) => {
    const [open, setOpen] = React.useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="outline">
                    <Logs className="w-4 h-4 mr-1" />
                    Logs
                </Button>
            </DialogTrigger>

            <DialogContent className="!max-w-6xl w-full">
                <DialogHeader>
                    <DialogTitle>Ticket Logs</DialogTitle>
                </DialogHeader>

                <div className="overflow-auto max-h-[70vh]">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>ID</TableHead>
                                <TableHead>User</TableHead>
                                <TableHead>Action</TableHead>
                                <TableHead>Old Values</TableHead>
                                <TableHead>New Values</TableHead>
                                <TableHead>Date</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {logs.map(log => (
                                <TableRow key={log.id}>
                                    <TableCell>{log.id}</TableCell>
                                    <TableCell>{log.user.name}</TableCell>
                                    <TableCell>{log.action}</TableCell>
                                    <TableCell>
                                        <pre className="whitespace-pre-wrap text-xs text-muted-foreground">{log.old_values}</pre>
                                    </TableCell>
                                    <TableCell>
                                        <pre className="whitespace-pre-wrap text-xs text-muted-foreground">{log.new_values}</pre>
                                    </TableCell>
                                    <TableCell>{log.created_at}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default TicketLogsModal;
