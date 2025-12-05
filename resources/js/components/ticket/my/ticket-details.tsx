import React, { useRef } from "react";
import { cn } from "@/lib/utils";
import { ArrowLeft, Dot, Pencil, User, Tag, Shield, Check, Trash, Trash2 } from "lucide-react";
import {
    Card, CardAction, CardContent,
    CardDescription, CardHeader, CardTitle
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import DateFormatter from "@/components/common/date-formatter";
import TicketFieldValue from "@/components/ticket/my/ticket-field-value";
import TicketFieldEditorForm from "@/components/ticket/my/ticket-field-editor-form";
import {
    Select, SelectContent, SelectItem,
    SelectTrigger, SelectValue
} from "@/components/ui/select";
import type { UseFormReturn } from "react-hook-form";
import type { Slide } from "yet-another-react-lightbox";

import {
    ImageSlide,
    VideoSlide,
} from "@/components/library/types";


interface TicketDetailsProps {
    selectedTicket: App.DTO.Ticket.TicketListDto;
    setSelectedTicket: (ticket: App.DTO.Ticket.TicketListDto | null) => void;
    isEditingField: boolean;
    setIsEditingField: (isEditing: boolean) => void;
    selectedTicketPriority: string | null;
    setSelectedTicketPriority: (priority: string) => void;
    handleApproveTicket: (ticketId: number) => void;
    handleUpdateTicket: (ticketId: number, fields: any) => void;
    handleDeleteTicket: (ticketId: number) => void;
    countries: App.DTO.CountryDto[];
    projects: App.DTO.ProjectDto[];
}

import Lightbox from "yet-another-react-lightbox";
import Video from "yet-another-react-lightbox/plugins/video";
import "yet-another-react-lightbox/styles.css";
import { useMemo, useState } from "react";

const TicketDetails = ({
                           selectedTicket,
                           setSelectedTicket,
                           isEditingField,
                           setIsEditingField,
                           selectedTicketPriority,
                           setSelectedTicketPriority,
                           handleApproveTicket,
                           handleUpdateTicket,
                           handleDeleteTicket,
                           countries,
                           projects,
                       }: TicketDetailsProps) => {
    const formRef = useRef<UseFormReturn<any> | null>(null);

    const handleSave = () => {
        formRef.current?.handleSubmit((data) => {
            handleUpdateTicket(selectedTicket.id, data);
            setIsEditingField(false);
        })();
    };

// Внутри компонента
    const [lightboxIndex, setLightboxIndex] = useState<number | null>(null);

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
                            className="flex items-center lg:hidden text-muted-foreground gap-1"
                            onClick={() => setSelectedTicket(null)}
                        >
                            <ArrowLeft className="size-5"/>
                            <span>Tickets list</span>
                        </div>
                        <div className="flex flex-col md:flex-row md:items-center">
                            <h3 className="text-base md:text-lg font-semibold">{selectedTicket.ticket_number}</h3>
                            <Dot className="hidden md:block"/>
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
                            <Dot/>
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
                    {selectedTicket.status.is_default && (
                        <CardAction className="flex gap-3">
                            {isEditingField ? (
                                <>
                                    <Button variant="outline" onClick={() => setIsEditingField(false)}>
                                        Cancel
                                    </Button>
                                </>
                            ) : (
                                <Button variant="outline" onClick={() => setIsEditingField(true)}>
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
                    )}
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
                                            {icon}
                                            {item.responsible_title}
                                        </span>
                                    );
                                })}
                            </div>
                        </div>

                        {/* Status */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Status:</span>
                            <div className="w-3/4 flex items-center gap-3 text-sm text-muted-foreground">
                                <Badge
                                    variant="outline"
                                    className={`px-1.5 py-0.5 text-xs border-${selectedTicket.status.color} text-${selectedTicket.status.color}`}
                                >
                                    {selectedTicket.status.name}
                                </Badge>
                                {selectedTicket.status.is_default && (
                                    <Button
                                        variant="secondary"
                                        size="sm"
                                        className="flex items-center gap-1"
                                        onClick={() => handleApproveTicket(selectedTicket.id)}
                                    >
                                        <Check className="size-4" />
                                        Approve
                                    </Button>
                                )}
                            </div>
                        </div>

                        {/* Result */}
                        {selectedTicket.result && (
                            <div className="flex items-center gap-4">
                                <span className="w-1/4 text-sm text-muted-foreground">Result:</span>
                                <span className="w-3/4 flex items-center gap-1 text-sm text-primary">
                                    {selectedTicket.result }
                                </span>
                            </div>
                        )}

                        {/* Priority */}
                        <div className="flex items-center gap-4">
                            <span className="w-1/4 text-sm text-muted-foreground">Ticket priority:</span>
                            {isEditingField ? (
                                <div className="w-3/4">
                                    <Select
                                        defaultValue={selectedTicket.priority}
                                        onValueChange={setSelectedTicketPriority}
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
                                    <Badge
                                        variant="outline"
                                        className={cn(
                                            "px-1.5 py-0.5 text-xs",
                                            selectedTicket.priority === "low" && "border-green text-green",
                                            selectedTicket.priority === "middle" && "border-yellow text-yellow",
                                            selectedTicket.priority === "high" && "border-red text-red"
                                        )}
                                    >
                                        {selectedTicket.priority}
                                    </Badge>
                                </span>
                            )}
                        </div>

                        {/* Custom Fields */}
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
                                <Button onClick={handleSave}>Save</Button>
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

export default TicketDetails;
