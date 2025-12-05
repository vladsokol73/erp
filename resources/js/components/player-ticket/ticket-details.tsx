import { ArrowLeft, Dot, User } from "lucide-react";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import DateFormatter from "@/components/common/date-formatter";
import React from "react";
import {cn} from "@/lib/utils";

interface TicketDetailsProps {
    ticket: App.DTO.Ticket.PlayerTicketListDto;
    onBack?: () => void;
}

const TicketDetails = ({ ticket, onBack }: TicketDetailsProps) => {
    return (
        <>
            {/* Верхняя часть */}
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
                            <h3 className="text-base md:text-lg font-semibold">
                                {ticket.ticket_number}
                            </h3>
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

            {/* Карточка с деталями */}
            <Card className="mt-4">
                <CardHeader className="border-b">
                    <CardTitle>Ticket Details</CardTitle>
                    <CardDescription>Details about this ticket</CardDescription>
                </CardHeader>

                <CardContent>
                    <div className="flex flex-col gap-4">
                        {/* User */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">User ID:</span>
                            <span className="w-3/4 flex items-center gap-1 text-sm text-primary">
                                <User className="w-4 h-4" />
                                {ticket.user.name}
                            </span>
                        </div>

                        {/* Player */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Player ID:</span>
                            <span className="w-3/4 text-sm">{ticket.player_id}</span>
                        </div>

                        {/* Type */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Type:</span>
                            <span className="w-3/4 text-sm">
                                <Badge variant="outline">{ticket.type}</Badge>
                            </span>
                        </div>

                        {/* Status */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Status:</span>
                            <span className="w-3/4 text-sm">
                                <Badge
                                        variant="outline"
                                        className={`px-1.5 py-0.5 text-xs border-${ticket.status.color} text-${ticket.status.color}`}
                                    >
                                    {ticket.status.name}
                                </Badge>
                            </span>
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

                        {/* Approved At */}
                        {ticket.approved_at && (
                            <div className="flex items-center gap-4">
                                <span className="w-1/4 text-sm text-muted-foreground">Approved At:</span>
                                <span className="w-3/4 text-sm">{ticket.approved_at}</span>
                            </div>
                        )}

                        {/* Result */}
                        {ticket.result && (
                            <div className="flex items-center gap-4">
                                <span className="w-1/4 text-sm text-muted-foreground">Result:</span>
                                <span className="w-3/4 text-sm">{ticket.result}</span>
                            </div>
                        )}

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
        </>
    );
};

export default TicketDetails;
