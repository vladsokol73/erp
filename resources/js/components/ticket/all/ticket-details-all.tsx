import React, { useEffect, useMemo, useRef, useState } from "react";
import {ArrowLeft, Check, Dot, Logs, Pencil, Shield, Tag, Trash2, User} from "lucide-react";
import {
    Card, CardHeader, CardTitle, CardDescription,
    CardAction, CardContent
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { cn } from "@/lib/utils";
import DateFormatter from "@/components/common/date-formatter";
import TicketFieldValue from "@/components/ticket/my/ticket-field-value";
import TicketFieldEditorForm from "@/components/ticket/my/ticket-field-editor-form";
import Lightbox from "yet-another-react-lightbox";
import Video from "yet-another-react-lightbox/plugins/video";
import "yet-another-react-lightbox/styles.css";
import type { UseFormReturn } from "react-hook-form";
import type { Slide } from "yet-another-react-lightbox";

import { ImageSlide, VideoSlide } from "@/components/library/types";
import TicketLogsModal from "./ticket-logs-modal";

interface TicketDetailsAllProps {
    selectedTicket: App.DTO.Ticket.TicketListAllDto;
    setSelectedTicket: (ticket: App.DTO.Ticket.TicketListAllDto | null) => void;
    isEditingField: boolean;
    setIsEditingField: (editing: boolean) => void;
    handleTicketChange: (
        ticketId: number,
        statusId: number,
        result: string | null,
        priority: string,
        fields: Record<string, any>
    ) => void;
    handleDeleteTicket: (ticketId: number) => void;
    countries: App.DTO.CountryDto[];
    projects: App.DTO.ProjectDto[];
}

const TicketDetailsAll = ({
                              selectedTicket,
                              setSelectedTicket,
                              isEditingField,
                              setIsEditingField,
                              handleTicketChange,
                              handleDeleteTicket,
                              countries,
                              projects
                          }: TicketDetailsAllProps) => {

    const [statusId, setStatusId] = useState<number>(selectedTicket.status.id);

    const [result, setResult] = useState<string>(selectedTicket.result ?? "");
    const [resultError, setResultError] = useState("");

    const [priority, setPriority] = useState<string>(selectedTicket.priority);
    const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);

    const formRef = useRef<UseFormReturn<any> | null>(null);

    useEffect(() => {
        setPriority(selectedTicket.priority);
    }, [selectedTicket]);

    const selectableStatuses = useMemo(() => {
        return selectedTicket.available_statuses.filter(
            status => status.is_final || (!status.is_default && !status.is_approval)
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

        formRef.current?.handleSubmit((data) => {
            handleTicketChange(
                selectedTicket.id,
                statusId,
                selectedStatus?.is_final ? result : null,
                priority,
                data
            );
            setIsEditingField(false);
        })();
    };

    const allFiles = useMemo(() => {
        return selectedTicket.fieldValues
            .filter(f => f.formField.type === "file" && !!f.value)
            .map(f => {
                const url = f.value;
                const ext = url?.split('.').pop()?.toLowerCase();
                if (!ext) return null;

                const isImage = ["jpg", "jpeg", "png", "gif", "webp"].includes(ext);
                const isVideo = ["mp4", "webm", "ogg"].includes(ext);

                return {
                    url,
                    type: isVideo ? "video" : isImage ? "image" : "file"
                };
            })
            .filter(Boolean) as { url: string; type: "image" | "video" | "file" }[];
    }, [selectedTicket.fieldValues]);

    const slides: Slide[] = allFiles.map((item): Slide => {
        if (item.type === "video") {
            return {
                type: "video",
                sources: [{ src: item.url, type: "video/mp4" }]
            } satisfies VideoSlide;
        } else {
            return {
                src: item.url
            } satisfies ImageSlide;
        }
    });

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
                                <DateFormatter dateString={selectedTicket.created_at} />
                            </span>
                        </div>
                        <div className="flex items-center">
                            <span className="text-xs md:text-sm text-muted-foreground">{selectedTicket.topic.category.name}</span>
                            <Dot />
                            <span className="text-xs md:text-sm text-muted-foreground">{selectedTicket.topic.name}</span>
                        </div>
                    </div>
                    <div>
                        <TicketLogsModal logs={selectedTicket.logs} />
                    </div>
                </div>
            </div>

            <div className="absolute left-0 w-full h-px bg-border" />

            <Card className="mt-4">
                <CardHeader className="border-b">
                    <CardTitle>Ticket Details</CardTitle>
                    <CardDescription>Details about this ticket</CardDescription>
                    <CardAction className="flex gap-3">
                        {isEditingField ? (
                            <Button variant="outline" onClick={() => setIsEditingField(false)}>Cancel</Button>
                        ) : (
                            <Button
                                variant="outline"
                                onClick={() => {
                                    setStatusId(selectedTicket.status.id);
                                    setResult(selectedTicket.result ?? "");
                                    setPriority(selectedTicket.priority);
                                    setIsEditingField(true);
                                }}
                            >
                                <Pencil className="w-4 h-4 mr-1" />
                                Edit
                            </Button>
                        )}

                        <Button
                            variant="outline"
                            className="text-red-500"
                            onClick={() => {
                                handleDeleteTicket(selectedTicket.id);
                            }}
                        >
                            <Trash2 className="w-4 h-4 mr-1" />
                            Delete
                        </Button>
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
                            {isEditingField ? (
                                <div className="w-3/4">
                                    <Select
                                        value={String(statusId)}
                                        onValueChange={(val) => setStatusId(Number(val))}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select status" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {selectedTicket.available_statuses.map(status => (
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
                                        className={cn(
                                            "px-1.5 py-0.5 text-xs",
                                            `border-${selectedTicket.status.color}`,
                                            `text-${selectedTicket.status.color}`
                                        )}
                                    >
                                        {selectedTicket.status.name}
                                    </Badge>
                                </div>
                            )}
                        </div>

                        {/* Result */}
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
                            <span className="w-1/4 text-sm text-muted-foreground">Priority:</span>
                            {isEditingField ? (
                                <div className="w-3/4">
                                    <Select
                                        value={priority}
                                        onValueChange={(val) => setPriority(val)}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select priority" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="low">Low</SelectItem>
                                            <SelectItem value="middle">Middle</SelectItem>
                                            <SelectItem value="high">High</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            ) : (
                                <span className="w-3/4 flex items-center gap-1 text-sm text-muted-foreground capitalize">
                                    <Badge variant="outline" className={cn(
                                        "px-1.5 py-0.5 text-xs",
                                        priority === "low" && "border-green text-green",
                                        priority === "middle" && "border-yellow text-yellow",
                                        priority === "high" && "border-red text-red"
                                    )}>
                                        {priority}
                                    </Badge>
                                </span>
                            )}
                        </div>

                        {/* Ticket fields */}
                        {isEditingField ? (
                            <TicketFieldEditorForm
                                fieldValues={selectedTicket.fieldValues}
                                countries={countries}
                                projects={projects}
                                formRef={formRef}
                            />
                        ) : (
                            selectedTicket.fieldValues.map((item) => {
                                const indexInAllFiles = allFiles.findIndex(f => f.url === item.value);
                                return (
                                    <TicketFieldValue
                                        key={item.id}
                                        item={item}
                                        countries={countries}
                                        projects={projects}
                                        isEditing={false}
                                        allFiles={allFiles}
                                        indexInAllFiles={indexInAllFiles >= 0 ? indexInAllFiles : null}
                                        onOpenLightbox={setLightboxIndex}
                                    />
                                );
                            })
                        )}

                        {/* Save */}
                        {isEditingField && (
                            <div className="flex justify-end">
                                <Button onClick={handleSubmit}>Save</Button>
                            </div>
                        )}
                    </div>
                </CardContent>

                {lightboxIndex !== null && (
                    <Lightbox
                        open={true}
                        close={() => setLightboxIndex(null)}
                        index={lightboxIndex}
                        slides={slides}
                        plugins={[Video]}
                    />
                )}
            </Card>
        </>
    );
};

export default TicketDetailsAll;
