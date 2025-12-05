import React, { useEffect } from "react";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    Table,
    TableBody,
    TableRow,
    TableCell,
} from "@/components/ui/table";
import {
    Tabs,
    TabsList,
    TabsTrigger,
    TabsContent,
} from "@/components/ui/tabs";
import { ImageOff } from "lucide-react";

interface Props {
    open: boolean;
    onClose: () => void;
    client: App.DTO.Client.ClientDetailsDto | null;
}

export default function ClientDetailsModal({ open, onClose, client }: Props) {
    const [activeTab, setActiveTab] = React.useState("Details");

    useEffect(() => {
        if (open) {
            setActiveTab("Details");
        }
    }, [open]);

    const rowClass =
        "odd:bg-muted/50 odd:hover:bg-muted/50 border-none hover:bg-transparent";

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="!max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Client Details</DialogTitle>
                    <DialogDescription>
                        Full information about client #{client?.id}
                    </DialogDescription>
                </DialogHeader>

                <div className="h-[550px]">
                    <Tabs value={activeTab} onValueChange={setActiveTab}>
                        <TabsList className="before:bg-border relative h-auto w-full gap-0.5 bg-transparent p-0 before:absolute before:inset-x-0 before:bottom-0 before:h-px">
                            <TabsTrigger
                                value="Details"
                                className="bg-muted overflow-hidden rounded-b-none border-x border-t py-2 data-[state=active]:z-10 data-[state=active]:shadow-none border-muted-foreground/20"
                            >
                                Details
                            </TabsTrigger>
                            <TabsTrigger
                                value="Preview"
                                className="bg-muted overflow-hidden rounded-b-none border-x border-t py-2 data-[state=active]:z-10 data-[state=active]:shadow-none border-muted-foreground/20"
                            >
                                Preview
                            </TabsTrigger>
                        </TabsList>

                        {/* DETAILS */}
                        <TabsContent value="Details" className="max-h-[500px] overflow-y-auto">
                            {client ? (
                                <Table>
                                    <TableBody>
                                        {Object.entries(client)
                                            .filter(([key]) => key !== "creative")
                                            .map(([key, value]) => {
                                                const renderValue = () => {
                                                    if (typeof value === "boolean") {
                                                        return (
                                                            <Badge className={value ? "bg-green/15 text-green" : "bg-red/15 text-red"}>
                                                                {value ? "True" : "False"}
                                                            </Badge>
                                                        );
                                                    }
                                                    if (typeof value === "number") {
                                                        return <Badge variant="secondary">{value}</Badge>;
                                                    }
                                                    if (typeof value === "string") {
                                                        return value.trim() !== "" ? (
                                                            <div className="whitespace-pre-wrap break-words break-all text-sm">
                                                                {value}
                                                            </div>
                                                        ) : (
                                                            <span className="text-muted-foreground italic">No data</span>
                                                        );
                                                    }
                                                    if (value === null || value === undefined) {
                                                        return <span className="text-muted-foreground italic">No data</span>;
                                                    }
                                                    return (
                                                        <div className="text-muted-foreground italic text-sm">
                                                            {JSON.stringify(value)}
                                                        </div>
                                                    );
                                                };

                                                return (
                                                    <TableRow key={key} className={rowClass}>
                                                        <TableCell className="font-medium whitespace-nowrap capitalize">
                                                            {key.replace(/_/g, " ")}
                                                        </TableCell>
                                                        <TableCell>{renderValue()}</TableCell>
                                                    </TableRow>
                                                );
                                            })}
                                    </TableBody>
                                </Table>
                            ) : (
                                <div className="text-muted-foreground italic text-sm">Loading...</div>
                            )}
                        </TabsContent>

                        {/* PREVIEW */}
                        <TabsContent value="Preview" className="pt-4 flex flex-col gap-6 items-center max-h-[500px] overflow-y-auto">
                            {client?.creative ? (
                                <>
                                    <img
                                        src={
                                            client.creative.type === "image"
                                                ? client.creative.url
                                                : client.creative.thumbnail ?? client.creative.url
                                        }
                                        alt="Client preview"
                                        className="max-w-full rounded border max-h-[300px]"
                                    />

                                    <Table className="w-full text-sm">
                                        <TableBody>
                                            <TableRow className={rowClass}>
                                                <TableCell className="font-medium whitespace-nowrap capitalize">Type</TableCell>
                                                <TableCell>
                                                    <Badge variant="secondary">{client.creative.type}</Badge>
                                                </TableCell>
                                            </TableRow>

                                            <TableRow className={rowClass}>
                                                <TableCell className="font-medium whitespace-nowrap capitalize">Code</TableCell>
                                                <TableCell className="break-all">{client.creative.code}</TableCell>
                                            </TableRow>

                                            <TableRow className={rowClass}>
                                                <TableCell className="font-medium whitespace-nowrap capitalize">Country</TableCell>
                                                <TableCell className="flex items-center gap-2">
                                                    <img
                                                        src={client.creative.country.img ?? ""}
                                                        alt={client.creative.country.name}
                                                        className="size-5 rounded-full"
                                                    />
                                                    {client.creative.country.name}
                                                </TableCell>
                                            </TableRow>

                                            <TableRow className={rowClass}>
                                                <TableCell className="font-medium whitespace-nowrap capitalize">Created At</TableCell>
                                                <TableCell>{client.creative.created_at}</TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </>
                            ) : (
                                <div className="flex flex-col items-center gap-2 text-sm text-center text-muted-foreground py-10">
                                    <ImageOff size={32} />
                                    No preview available
                                </div>
                            )}
                        </TabsContent>
                    </Tabs>
                </div>

                <DialogFooter className="pt-4">
                    <Button variant="secondary" onClick={onClose}>
                        Close
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
