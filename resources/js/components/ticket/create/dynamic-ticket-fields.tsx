import React from "react";
import {
    Control,
    FieldErrors,
} from "react-hook-form";

import { TextField } from "@/components/ticket/fields/text-field";
import { TextareaField } from "@/components/ticket/fields/textarea-field";
import { NumberFieldAria } from "@/components/ticket/fields/number-field-aria";
import { SelectField } from "@/components/ticket/fields/select-field";
import { MultiSelectField } from "@/components/ticket/fields/multi-select-field";
import { CountryField } from "@/components/ticket/fields/country-field";
import { FileField } from "@/components/ticket/fields/file-field";
import { DateField } from "@/components/ticket/fields/date-field";
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/components/ui/form";
import { ProjectField } from "../fields/project-field";

interface Props {
    fields: App.DTO.Ticket.TicketFormFieldDto[];
    control: Control<any>;
    errors: FieldErrors<any>;
    countryOptions?: App.DTO.CountryDto[];
    projectOptions?: App.DTO.ProjectDto[];
}

export const DynamicTicketFields: React.FC<Props> = ({
                                                         fields,
                                                         control,
                                                         countryOptions,
                                                         projectOptions,
                                                     }) => {
    // Функция рендера конкретного поля
    const renderFieldComponent = (
        field: App.DTO.Ticket.TicketFormFieldDto,
        value: any,
        onChange: (val: any) => void
    ) => {
        const props = { value, onChange };

        switch (field.type) {
            case "text":
                return <TextField {...props} />;
            case "textarea":
                return <TextareaField {...props} />;
            case "number":
                return <NumberFieldAria {...props} />;
            case "select":
                return (
                    <SelectField
                        options={field.options ?? []}
                        {...props}
                    />
                );
            case "multiselect":
                return (
                    <MultiSelectField
                        options={(field.options ?? []).map((o: any) => ({ value: o, label: o }))}
                        {...props}
                    />
                );
            case "country":
                // Если поле type === "country", то мы пробрасываем либо глобальный countryOptions (из пропов),
                // либо пытаемся взять field.options как массив CountryOption[]
                const opts: App.DTO.CountryDto[] | undefined = countryOptions
                    ? countryOptions
                    : Array.isArray(field.options)
                        ? (field.options as App.DTO.CountryDto[])
                        : undefined;

                return (
                    <CountryField
                        options={opts}
                        {...props}
                    />
                );
            case "project":
                // пробрасываем глобальный projectOptions (из пропов)
                // или пытаемся взять field.options как массив ProjectOption[]
                const optsProject: App.DTO.ProjectDto[] | undefined =
                    projectOptions ??
                    (Array.isArray(field.options) ? (field.options as App.DTO.ProjectDto[]) : undefined);

                return (
                    <ProjectField
                        options={optsProject}
                        {...props}
                    />
                );
            case "file":
                return <FileField {...props} />;
            case "date":
                return <DateField {...props} />;
            default:
                return (
                    <div className="text-muted-foreground italic">
                        Unsupported field type: {field.type}
                    </div>
                );
        }
    };

    return (
        <div className="space-y-4">
            {fields.map((field) => (
                <FormField
                    key={field.id}
                    control={control}
                    name={field.id.toString()}
                    render={({ field: controllerField }) => (
                        <FormItem>
                            <FormLabel>{field.label}</FormLabel>
                            <FormControl>
                                {renderFieldComponent(
                                    field,
                                    controllerField.value,
                                    controllerField.onChange
                                )}
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />
            ))}
        </div>
    );
};
