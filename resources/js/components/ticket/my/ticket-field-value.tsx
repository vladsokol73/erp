import React, { useMemo, useState, Fragment } from "react";
import { useFormContext, Controller } from "react-hook-form";
import { Badge } from "@/components/ui/badge";

import { TextField } from "@/components/ticket/fields/text-field";
import { TextareaField } from "@/components/ticket/fields/textarea-field";
import { SelectField } from "@/components/ticket/fields/select-field";
import { MultiSelectField } from "@/components/ticket/fields/multi-select-field";
import { CountryField } from "@/components/ticket/fields/country-field";
import { DateField } from "@/components/ticket/fields/date-field";
import { NumberFieldAria } from "@/components/ticket/fields/number-field-aria";
import { ProjectField } from "@/components/ticket/fields/project-field";
import { File } from "lucide-react";
import { Button } from "@/components/ui/button";
import MediaPreview from "../../library/media-preview";

import "yet-another-react-lightbox/styles.css";

import {SmartFileField} from "@/components/ticket/fields/smart-file-field";

interface Props {
    item: App.DTO.Ticket.TicketFieldValuesListDto;
    countries: App.DTO.CountryDto[];
    projects: App.DTO.ProjectDto[];
    isEditing: boolean;
    allFiles: { url: string; type: "image" | "video" | "file" }[];
    indexInAllFiles: number | null;
    onOpenLightbox: (index: number) => void;
}

const TicketFieldValue: React.FC<Props> = ({ item, countries, projects, isEditing, allFiles, indexInAllFiles, onOpenLightbox }) => {

    const { formField } = item;
    const form = useFormContext();
    const fieldName = `field_${formField.id}`;

    const renderEditor = () => (
        <Controller
            name={fieldName}
            control={form.control}
            render={({ field, fieldState }) => {
                const { value, onChange } = field;
                const error = fieldState.error?.message;

                switch (formField.type) {
                    case "text":
                        return <TextField value={value} onChange={onChange} error={error} />;

                    case "textarea":
                        return <TextareaField value={value} onChange={onChange} error={error} />;

                    case "number":
                        return <NumberFieldAria value={value} onChange={onChange} error={error} />;

                    case "select":
                        return (
                            <SelectField
                                value={value}
                                onChange={onChange}
                                options={formField.options ?? []}
                                error={error}
                            />
                        );

                    case "multiselect":
                        return (
                            <MultiSelectField
                                value={value}
                                onChange={onChange}
                                options={(formField.options ?? []).map((o) => ({ value: o, label: o }))}
                                error={error}
                            />
                        );

                    case "country":
                        return <CountryField value={value} onChange={onChange} options={countries} error={error} />;

                    case "project":
                        return <ProjectField value={value} onChange={onChange} options={projects} error={error} />;

                    case "date":
                        return <DateField value={value} onChange={onChange} error={error} />;

                    case "file":
                        return (
                            <SmartFileField
                                value={value}
                                onChange={onChange}
                                error={error}
                            />
                        );

                    default:
                        return <div className="text-muted-foreground italic">Unsupported field</div>;
                }
            }}
        />

    );

    const renderDisplayValue = () => {
        switch (formField.type) {
            case "text":
            case "textarea":
            case "select":
                return <div>{item.value || "-"}</div>;
            case "number":
                return <div>{item.value ?? "-"}</div>;
            case "multiselect":
                try {
                    const values = item.value ? JSON.parse(item.value) : [];
                    return values.length > 0 ? (
                        <div className="flex flex-wrap gap-1">
                            {values.map((val: string, index: number) => (
                                <Badge key={index} variant="secondary">
                                    {val}
                                </Badge>
                            ))}
                        </div>
                    ) : (
                        <div>-</div>
                    );
                } catch {
                    return <div>-</div>;
                }
            case "country":
                const country = countries.find((c) => c.id === Number(item.value));
                return (
                    <div className="flex items-center gap-2">
                        {country?.img && (
                            <img className="size-6 rounded-full" src={country.img} alt={country.name} />
                        )}
                        <span>{country?.name ?? "-"}</span>
                    </div>
                );
            case "project":
                const project = projects.find((p) => p.id === Number(item.value));
                return <div>{project?.name ?? "-"}</div>;
            case "date":
                return <div>{item.value ? new Date(item.value).toLocaleDateString() : "-"}</div>;
            case "file":
                const value = item.value;
                const matches = value?.match(/\.(\w+)$/);
                const extension = matches?.[1];
                const ext = extension?.toLowerCase() ?? "";
                const isVideo = ["mp4", "webm", "ogg"].includes(ext);
                const isImage = ["jpg", "jpeg", "png", "gif", "webp"].includes(ext);

                return (
                    <span>
            {value ? (
                <div className="flex flex-col gap-2 p-2">
                    {isImage && (
                        <div className="w-96 flex flex-col gap-2">
                            <MediaPreview
                                type="image"
                                src={value}
                                onPreviewOpen={() => {
                                    if (indexInAllFiles !== null) onOpenLightbox(indexInAllFiles);
                                }}
                                thumbnailUrl={value}
                            />
                            <a href={value} target="_blank" rel="noopener noreferrer" download>
                                <Button className="w-full" variant="link">
                                    <File className="w-6 h-6 mr-2" />
                                    <span>Download</span>
                                </Button>
                            </a>
                        </div>
                    )}
                    {isVideo && (
                        <div className="w-96 flex flex-col gap-2">
                            <MediaPreview
                                type="video"
                                src={value}
                                onPreviewOpen={() => {
                                    if (indexInAllFiles !== null) onOpenLightbox(indexInAllFiles);
                                }}
                            />
                            <a href={value} target="_blank" rel="noopener noreferrer" download>
                                <Button className="w-full" variant="link">
                                    <File className="w-6 h-6 mr-2" />
                                    <span>Download</span>
                                </Button>
                            </a>
                        </div>
                    )}
                    {!isImage && !isVideo && (
                        <a href={value} target="_blank" rel="noopener noreferrer" download>
                            <Button className="w-48" variant="link">
                                <File className="w-6 h-6 mr-2" />
                                <span>Download</span>
                            </Button>
                        </a>
                    )}
                </div>
            ) : (
                <div>File not found</div>
            )}
        </span>
                );
            default:
                return <div className="text-muted-foreground italic">Unsupported field</div>;
        }
    };

    return (
        <div className="flex items-start gap-4">
            <span className="w-1/4 text-sm text-muted-foreground pt-0.5">{formField.label}:</span>
            <div className="w-3/4">{isEditing ? renderEditor() : renderDisplayValue()}</div>
        </div>
    );
};

export default TicketFieldValue;
