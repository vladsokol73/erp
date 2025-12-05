import React from "react";
import { useForm, FormProvider, UseFormReturn } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import TicketFieldValue from "@/components/ticket/my/ticket-field-value";
import { generateZodSchemaFromRules } from "@/lib/generate-zod-schema-from-rules";
import { z, ZodTypeAny } from "zod";

interface Props {
    fieldValues: App.DTO.Ticket.TicketFieldValuesListDto[];
    countries: App.DTO.CountryDto[];
    projects: App.DTO.ProjectDto[];
    formRef?: React.MutableRefObject<UseFormReturn<any> | null>;
}

function prepareDefaultValue(field: App.DTO.Ticket.TicketFieldValuesListDto): any {
    const { type } = field.formField;
    const raw = field.value;

    switch (type) {
        case "number":
            return raw !== null && raw !== undefined ? Number(raw) : undefined;

        case "date":
            return raw ? new Date(raw) : undefined;

        case "multiselect":
            try {
                return raw ? JSON.parse(raw) : [];
            } catch {
                return [];
            }

        case "file":
            // Проверка: если raw — строка и это ссылка
            if (/^https?:\/\//.test(raw)) {
                return raw;
            }
            return null;
        case "country":
            return raw !== null && raw !== undefined ? Number(raw) : undefined;

        case "project":
            return raw !== null && raw !== undefined ? Number(raw) : undefined;

        default:
            return raw ?? "";
    }
}

export default function TicketFieldEditorForm({ fieldValues, countries, projects, formRef}: Props) {
    const shape: Record<string, ZodTypeAny> = {};

    for (const field of fieldValues) {
        const key = `field_${field.formField.id}`;
        shape[key] = generateZodSchemaFromRules(
            field.formField.validation_rules || [],
            field.formField.is_required,
            field.formField.type
        );
    }

    const schema = z.object(shape);

    const form = useForm({
        resolver: zodResolver(schema),
        defaultValues: Object.fromEntries(
            fieldValues.map((field) => [
                `field_${field.formField.id}`,
                prepareDefaultValue(field),
            ])
        )
    });

    // Прокидываем наружу ссылку на form
    if (formRef) {
        formRef.current = form;
    }

    return (
        <FormProvider {...form}>
            <div className="flex flex-col gap-4">
                {fieldValues.map((field) => (
                    <TicketFieldValue
                        key={field.id}
                        item={field}
                        countries={countries}
                        projects={projects}
                        isEditing={true}
                        allFiles={[]}
                        indexInAllFiles={null}
                        onOpenLightbox={() => {}}
                    />
                ))}
            </div>
        </FormProvider>
    );
}
