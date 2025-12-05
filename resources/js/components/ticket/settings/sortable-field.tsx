import React from "react";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { GripVertical} from "lucide-react";

import { TextField } from "../fields/text-field";
import { NumberFieldAria } from "../fields/number-field-aria";
import { TextareaField } from "../fields/textarea-field";
import { SelectField } from "../fields/select-field";
import { MultiSelectField } from "../fields/multi-select-field";
import { CountryField } from "../fields/country-field";
import { FileField } from "../fields/file-field";
import { DateField } from "../fields/date-field";
import { ProjectField } from "../fields/project-field";



export interface SortableFieldProps {
    field: {
        id: number;
        name: string;
        label: string;
        type: string;
        options?: string[];
    };
    onRemove: (id: number) => void;
}

export const SortableField: React.FC<SortableFieldProps> = ({ field, onRemove }) => {
    const {
        attributes,
        listeners,
        setNodeRef,
        transform,
        transition,
    } = useSortable({ id: field.id.toString() });

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
    };

    const renderFieldInput = () => {
        switch (field.type) {
            case "text":
                return (
                    <TextField
                        label={field.label}
                        placeholder={`Enter ${field.label.toLowerCase()}`}
                    />
                );

            case "number":
                return (
                    <NumberFieldAria
                        label={field.label}
                        placeholder={`Enter ${field.label.toLowerCase()}`}
                    />
                );

            case "textarea":
                return (
                    <TextareaField
                        label={field.label}
                        placeholder={`Enter ${field.label.toLowerCase()}`}
                    />
                );

            case "select":
                return (
                    <SelectField
                        label={field.label}
                        options={field.options ?? []}
                        placeholder={`Select ${field.label.toLowerCase()}`}
                    />
                );

            case "multiselect":
                return (
                    <MultiSelectField
                        placeholder={`Select ${field.label.toLowerCase()}`}
                        label={field.label}
                        options={
                            (field.options ?? []).map(opt => ({
                                value: opt,
                                label: opt,
                            }))
                        }
                    />
                );

            case "country":
                return (
                    <CountryField
                        label={field.label}
                    />
                );

            case "file":
                return (
                    <FileField
                        label={field.label}
                    />
                );

            case "date":
                return (
                    <DateField
                        label={field.label}
                    />
                );

            case "project":
                return (
                    <ProjectField
                        label={field.label}
                    />
                );

            default:
                return (
                    <div className="text-muted-foreground text-sm italic">
                        Unsupported field type: {field.type}
                    </div>
                );
        }
    };


    return (
        <div
            ref={setNodeRef}
            style={style}
            {...attributes}
            className="flex flex-col gap-2 border border-dashed px-3 py-2 rounded-md bg-input/30 shadow-sm"
        >
            <div className="flex items-center justify-between">
                <div {...listeners} className="flex items-center gap-2 cursor-grab">
                    <span
                        className="text-muted-foreground hover:text-primary"
                    >
                        <GripVertical className="w-4 h-4" />
                    </span>
                    <span className="text-sm font-semibold capitalize">{field.name}</span>
                </div>

                <button
                    type="button"
                    onClick={() => onRemove(field.id)}
                    className="text-sm text-muted-foreground hover:text-destructive"
                >
                    ✕
                </button>
            </div>

            {renderFieldInput()}
        </div>
    );
};
