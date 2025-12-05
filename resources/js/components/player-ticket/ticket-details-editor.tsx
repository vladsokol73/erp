import React, {useMemo, useState} from "react";
import {ArrowLeft, Dot, Pencil, User} from "lucide-react";
import {Card, CardAction, CardContent, CardDescription, CardHeader, CardTitle} from "@/components/ui/card";
import {Badge} from "@/components/ui/badge";
import {Button} from "@/components/ui/button";
import DateFormatter from "@/components/common/date-formatter";
import {Select, SelectContent, SelectItem, SelectTrigger, SelectValue} from "@/components/ui/select";
import {Textarea} from "@/components/ui/textarea";
import ProductLogsDialog from "@/components/player-ticket/product-logs-dialog";

interface TicketDetailsEditorProps {
    ticket: App.DTO.Ticket.PlayerTicketListDto & { product_logs?: App.DTO.Log.ProductLogListDto[] };
    onBack?: () => void;
    availableStatuses?: App.DTO.Ticket.PlayerTicketStatusDto[];
    onSubmit: (ticketId: number, statusName: string, result: string | null) => void;
}

const TicketDetailsEditor = ({
                                 ticket,
                                 onBack,
                                 availableStatuses,
                                 onSubmit,
                             }: TicketDetailsEditorProps) => {
    const [isEditing, setIsEditing] = useState(false);
    const [statusName, setStatusName] = useState<string | null>(ticket.status?.name ?? null);
    const [result, setResult] = useState<string>(ticket.result ?? "");
    const [logsOpen, setLogsOpen] = useState(false);

    const selectableStatuses = useMemo<App.DTO.Ticket.PlayerTicketStatusDto[]>(() => {
        return availableStatuses ?? [];
    }, [availableStatuses]);

    const handleStartEdit = () => {
        setStatusName(ticket.status?.name ?? null);
        setResult(ticket.result ?? "");
        setIsEditing(true);
    };

    const handleCancel = () => {
        setStatusName(ticket.status?.name ?? null);
        setResult(ticket.result ?? "");
        setIsEditing(false);
    };

    const handleSave = () => {
        const normalizedResult = result.trim() === "" ? null : result;
        const finalStatusName = (statusName ?? ticket.status?.name);
        if (!finalStatusName) {
            setIsEditing(false);
            return;
        }
        onSubmit(ticket.id, finalStatusName, normalizedResult);
        setIsEditing(false);
    };

    return (
        <>
            <div className="flex flex-col gap-2 pb-4">
                <div className="flex justify-between items-center gap-4">
                    <div className="flex flex-col gap-2">
                        {onBack && (
                            <div
                                className="flex items-center lg:hidden text-muted-foreground gap-1 cursor-pointer"
                                onClick={onBack}
                            >
                                <ArrowLeft className="size-5" />
                                <span>Tickets list</span>
                            </div>
                        )}
                        <div className="flex flex-col md:flex-row md:items-center">
                            <h3 className="text-base md:text-lg font-semibold">{ticket.ticket_number}</h3>
                            <Dot className="hidden md:block" />
                            <span className="text-sm text-muted-foreground">
                <DateFormatter
                    variant="full"
                    className="text-sm text-muted-foreground"
                    dateString={ticket.created_at}
                />
              </span>
                        </div>
                    </div>
                </div>
            </div>

            <div className="absolute left-0 w-full h-px bg-border" />

            <Card className="mt-4">
                <CardHeader className="border-b">
                    <CardTitle>Ticket Details</CardTitle>
                    <CardDescription>Details about this ticket</CardDescription>

                    <CardAction className="flex items-center gap-2">
                        {!isEditing && (
                            <Button variant="outline" onClick={() => setLogsOpen(true)}>
                                Product logs
                            </Button>
                        )}
                        {isEditing ? (
                            <div className="flex gap-2">
                                <Button variant="outline" onClick={handleCancel}>Cancel</Button>
                                <Button onClick={handleSave}>Save</Button>
                            </div>
                        ) : (
                            <Button variant="outline" onClick={handleStartEdit}>
                                <Pencil className="w-4 h-4 mr-1" />
                                Edit
                            </Button>
                        )}
                    </CardAction>
                </CardHeader>

                <CardContent>
                    <div className="flex flex-col gap-4">
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">User:</span>
                            <span className="w-3/4 flex items-center gap-1 text-sm text-primary">
                <User className="w-4 h-4" />
                                {ticket.user.name}
              </span>
                        </div>

                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Player ID:</span>
                            <span className="w-3/4 text-sm">{ticket.player_id}</span>
                        </div>

                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Type:</span>
                            <span className="w-3/4 text-sm">
                <Badge variant="outline">{ticket.type}</Badge>
              </span>
                        </div>

                        <div className="flex items-start gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground pt-1">Status:</span>
                            {isEditing ? (
                                <div className="w-3/4">
                                    <Select
                                        value={statusName ?? ticket.status?.name ?? undefined}
                                        onValueChange={(val) => setStatusName(val)}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {selectableStatuses.map((s) => (
                                                <SelectItem key={s.name} value={s.name}>
                                                    {s.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>
                            ) : (
                                <div className="w-3/4">
                                    <Badge
                                        variant="outline"
                                        className={`px-1.5 py-0.5 text-xs border-${ticket.status.color} text-${ticket.status.color}`}
                                    >
                                        {ticket.status.name}
                                    </Badge>
                                </div>
                            )}
                        </div>


                        {/* Telegram ID */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Telegram ID:</span>
                            <div className="flex flex-col w-3/4 gap-1">
                                <span className="text-sm font-medium">{ticket.tg_id}</span>
                                { (!ticket.is_valid_tg_id) && (
                                    <span className="text-sm text-destructive">Telegram ID is not valid</span>
                                )}
                            </div>
                        </div>

                        {/* Sum */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Sum:</span>
                            <div className="flex flex-col w-3/4 gap-1">
                                <span className="text-sm font-medium">{ticket.sum}</span>
                                { (!ticket.is_valid_sum) && (
                                    <span className="text-sm text-destructive">Sum is not valid</span>
                                )}
                            </div>
                        </div>

                        {ticket.approved_at && (
                            <div className="flex items-center gap-4">
                                <span className="w-1/4 text-sm text-muted-foreground">Approved At:</span>
                                <span className="w-3/4 text-sm">{ticket.approved_at}</span>
                            </div>
                        )}

                        <div className="flex items-start gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground pt-1">Result:</span>
                            {isEditing ? (
                                <div className="w-3/4">
                                    <Textarea
                                        value={result}
                                        onChange={(e) => setResult(e.target.value)}
                                        placeholder="Write result..."
                                    />
                                </div>
                            ) : ticket.result ? (
                                <div className="w-3/4 text-sm whitespace-pre-wrap">{ticket.result}</div>
                            ) : (
                                <div className="w-3/4 text-sm text-muted-foreground italic">No result</div>
                            )}
                        </div>

                        {/* Screenshot */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Screenshot:</span>
                            <span className="w-3/4 text-sm break-all">
                                {ticket.screen_url ? (
                                    <a
                                        href={ticket.screen_url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-block"
                                    >
                                        <img
                                            src={ticket.screen_url}
                                            alt="Ticket screenshot"
                                            className="h-full  rounded border object-contain bg-muted"
                                        />
                                    </a>
                                ) : (
                                    <span className="text-muted-foreground">No screenshot</span>
                                )}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <ProductLogsDialog
                open={logsOpen}
                onOpenChange={setLogsOpen}
                logs={ticket.product_logs}
            />
        </>
    );
};

export default TicketDetailsEditor;
