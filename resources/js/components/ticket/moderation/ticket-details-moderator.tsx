import React, { useMemo, useState } from "react";
import { cn } from "@/lib/utils";
import { ArrowLeft, Dot, Pencil, User, Tag, Shield } from "lucide-react";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    CardAction,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import DateFormatter from "@/components/common/date-formatter";
import TicketFieldValue from "@/components/ticket/my/ticket-field-value";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";

interface TicketDetailsModeratorProps {
    selectedTicket: App.DTO.Ticket.TicketListDto;
    setSelectedTicket: (ticket: App.DTO.Ticket.TicketListDto | null) => void;
    isEditingField: boolean;
    setIsEditingField: (editing: boolean) => void;
    handleTicketChange: (ticketId: number, statusId: number, result: string | null) => void;
    countries: App.DTO.CountryDto[];
    projects: App.DTO.ProjectDto[];
}

const TicketDetailsModerator = ({
                                    selectedTicket,
                                    setSelectedTicket,
                                    isEditingField,
                                    setIsEditingField,
                                    handleTicketChange,
                                    countries,
                                    projects,
                                }: TicketDetailsModeratorProps) => {
    const [statusId, setStatusId] = useState<number | null>(null);

    const [result, setResult] = useState<string>("");
    const [resultError, setResultError] = useState("");

    const selectableStatuses = useMemo(() => {
        return selectedTicket.available_statuses.filter(
            (status) =>
                status.is_final === true ||
                (
                    status.is_final === false &&
                    status.is_default === false &&
                    status.is_approval === false
                )
        );
    }, [selectedTicket.available_statuses]);

    const selectedStatus = useMemo(() => {
        return selectableStatuses.find(s => s.id === statusId) ?? null;
    }, [statusId, selectableStatuses]);

    const handleSubmit = () => {
        if (selectedStatus?.is_final && !result.trim()) {
            setResultError("Result is required");
            return;
        }

        setResultError("");
        if (statusId !== null) {
            handleTicketChange(selectedTicket.id, statusId, selectedStatus?.is_final ? result : null);
            setIsEditingField(false);
        }
    };

    return (
        <>
            <div className="flex flex-col gap-2 pb-4">
                <div className="flex justify-between items-center gap-4">
                    <div className="flex flex-col gap-2">
                        <div
                            className="flex items-center lg:hidden text-muted-foreground gap-1 cursor-pointer"
                            onClick={() => setSelectedTicket(null)}
                        >
                            <ArrowLeft className="size-5" />
                            <span>Tickets list</span>
                        </div>
                        <div className="flex flex-col md:flex-row md:items-center">
                            <h3 className="text-base md:text-lg font-semibold">{selectedTicket.ticket_number}</h3>
                            <Dot className="hidden md:block" />
                            <span className="text-sm text-muted-foreground">
                                <DateFormatter
                                    variant="full"
                                    className="text-sm text-muted-foreground"
                                    dateString={selectedTicket.created_at}
                                />
                            </span>
                        </div>
                        <div className="flex items-center">
                            <span className="text-xs md:text-sm text-muted-foreground">{selectedTicket.topic.category.name}</span>
                            <Dot />
                            <span className="text-xs md:text-sm text-muted-foreground">{selectedTicket.topic.name}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div className="absolute left-0 w-full h-px bg-border" />

            <Card className="mt-4">
                <CardHeader className="border-b">
                    <CardTitle>Ticket Details</CardTitle>
                    <CardDescription>Details about this ticket</CardDescription>

                    <CardAction>
                        {isEditingField ? (
                            <Button variant="outline" onClick={() => setIsEditingField(false)}>
                                Cancel
                            </Button>
                        ) : (
                            <Button variant="outline" onClick={() => {
                                setStatusId(selectedTicket.status.id);
                                setResult(selectedTicket.result ?? "");
                                setIsEditingField(true);
                            }}>
                                <Pencil className="w-4 h-4 mr-1" />
                                Edit
                            </Button>
                        )}
                    </CardAction>
                </CardHeader>

                <CardContent>
                    <div className="flex flex-col gap-4">
                        {/* Customer */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Customer:</span>
                            <span className="w-3/4 flex items-center gap-1 text-sm text-primary">
                                <User className="w-4 h-4" />
                                {selectedTicket.user.name}
                            </span>
                        </div>

                        {/* Executor */}
                        <div className="flex items-start gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground pt-0.5">Executor:</span>
                            <div className="w-3/4 flex gap-2 text-sm text-primary">
                                {selectedTicket.responsible.map((item, index) => {
                                    const icons: Record<string, JSX.Element> = {
                                        User: <User className="w-4 h-4" />,
                                        Role: <Tag className="w-4 h-4" />,
                                        Permission: <Shield className="w-4 h-4" />,
                                    };
                                    const icon = item.responsible_model_name && icons[item.responsible_model_name] || null;

                                    return (
                                        <span key={index} className="flex items-center gap-1">
                                            {icon && <span>{icon}</span>}
                                            {item.responsible_title}
                                        </span>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Status */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Status:</span>
                            {
                                isEditingField ? (
                                    <div className="w-3/4">
                                        <Select
                                            value={
                                                selectedTicket.status.is_default || selectedTicket.status.is_approval ?
                                                    undefined
                                                    :
                                                    String(statusId)
                                            }
                                            onValueChange={(val) => setStatusId(Number(val))}
                                        >
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="Select status" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {selectableStatuses.map(status => (
                                                    <SelectItem key={status.id} value={String(status.id)}>
                                                        {status.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                ) : (
                                    <div className="w-3/4 flex items-center gap-3 text-sm text-muted-foreground">
                                        <Badge
                                            variant="outline"
                                            className={`px-1.5 py-0.5 text-xs border-${selectedTicket.status.color} text-${selectedTicket.status.color}`}
                                        >
                                            {selectedTicket.status.name}
                                        </Badge>
                                    </div>
                                )
                            }
                        </div>

                        {/* Result (если финальный статус) */}
                        {isEditingField && selectedStatus?.is_final ? (
                            <div className="flex items-start gap-4">
                                <span className="w-1/4 text-sm text-muted-foreground pt-1">Result:</span>
                                <div className="w-3/4 flex flex-col gap-2">
                                    <Textarea
                                        value={result}
                                        onChange={(e) => {
                                            setResult(e.target.value);
                                            if (e.target.value.trim()) {
                                                setResultError("");
                                            }
                                        }}
                                        placeholder="Write result..."
                                    />
                                    {resultError && (
                                        <span className="text-sm text-destructive">{resultError}</span>
                                    )}
                                </div>
                            </div>
                        ) : (!isEditingField && selectedTicket.status.is_final && selectedTicket.result) ? (
                            <div className="flex items-start gap-4">
                                <span className="w-1/4 text-sm text-muted-foreground pt-1">Result:</span>
                                <div className="w-3/4 text-sm whitespace-pre-wrap">
                                    {selectedTicket.result}
                                </div>
                            </div>
                        ) : null}


                        {/* Priority */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Ticket priority:</span>
                            <span className="w-3/4 flex items-center gap-1 text-sm text-muted-foreground capitalize">
                                <Badge variant="outline" className={cn(
                                    "px-1.5 py-0.5 text-xs",
                                    selectedTicket.priority === "low" && "border-green text-green",
                                    selectedTicket.priority === "middle" && "border-yellow text-yellow",
                                    selectedTicket.priority === "high" && "border-red text-red"
                                )}>
                                    {selectedTicket.priority}
                                </Badge>
                            </span>
                        </div>

                        {/* Ticket fields */}
                        {selectedTicket.fieldValues.map((item) => (
                            <TicketFieldValue
                                key={item.id}
                                item={item}
                                countries={countries}
                                projects={projects}
                                isEditing={false}
                                allFiles={[]}
                                indexInAllFiles={0}
                                onOpenLightbox={() => {}}
                            />
                        ))}

                        {/* Save */}
                        {isEditingField && (
                            <div className="flex justify-end">
                                <Button onClick={handleSubmit}>
                                    Save
                                </Button>
                            </div>
                        )}
                    </div>
                </CardContent>
            </Card>
        </>
    );
};

export default TicketDetailsModerator;
