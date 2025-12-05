import * as React from "react";
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";

interface ProductLogsDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    logs?: App.DTO.Log.ProductLogListDto[];
}

const ProductLogsDialog: React.FC<ProductLogsDialogProps> = ({ open, onOpenChange, logs }) => {
    const items = logs ?? [];

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="!max-w-6xl">
                <DialogHeader>
                    <DialogTitle>Product logs</DialogTitle>
                    <DialogDescription>Related product operations</DialogDescription>
                </DialogHeader>

                <div className="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHead className="whitespace-nowrap">ID</TableHead>
                                <TableHead className="whitespace-nowrap">Player ID</TableHead>
                                <TableHead className="whitespace-nowrap">Status</TableHead>
                                <TableHead className="whitespace-nowrap">C2D Channel</TableHead>
                                <TableHead className="whitespace-nowrap">Telegram ID</TableHead>
                                <TableHead className="whitespace-nowrap">Product ID</TableHead>
                                <TableHead className="whitespace-nowrap">Dep Sum</TableHead>
                                <TableHead className="whitespace-nowrap">Operator ID</TableHead>
                                <TableHead className="whitespace-nowrap">Created At</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {items.length === 0 ? (
                                <TableRow className="hover:bg-transparent">
                                    <TableCell colSpan={9} className="text-center text-sm text-muted-foreground">
                                        No product logs
                                    </TableCell>
                                </TableRow>
                            ) : (
                                items.map((log) => (
                                    <TableRow key={log.id} className="hover:bg-transparent">
                                        <TableCell>{log.id}</TableCell>
                                        <TableCell>{log.player_id}</TableCell>
                                        <TableCell><Badge variant="outline">{log.status}</Badge></TableCell>
                                        <TableCell>{log.c2d_channel_id}</TableCell>
                                        <TableCell>{log.tg_id}</TableCell>
                                        <TableCell>{log.prod_id}</TableCell>
                                        <TableCell>{log.dep_sum}</TableCell>
                                        <TableCell>{log.operator_id}</TableCell>
                                        <TableCell>{log.created_at}</TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default ProductLogsDialog;
