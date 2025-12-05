// Добавляем импорт хука useApi
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import React, { useMemo, useState } from "react";
import { Head } from "@inertiajs/react";
import { toast } from "sonner";

import {
    Form,
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormMessage,
} from "@/components/ui/form";
import {
    Select,
    SelectTrigger,
    SelectContent,
    SelectItem,
    SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardDescription,
} from "@/components/ui/card";
import AppLayout from "@/components/layouts/app-layout";
import { DynamicTicketFields } from "@/components/ticket/create/dynamic-ticket-fields";
import { generateZodSchemaFromRules } from "@/lib/generate-zod-schema-from-rules";
import useApi from "@/hooks/use-api";
import {route} from "ziggy-js";
import { router } from '@inertiajs/react'
import {cn} from "@/lib/utils";
import {LoaderCircle} from "lucide-react";

interface Props {
    categories: App.DTO.Ticket.TicketCategoryDto[];
    topics: App.DTO.Ticket.TicketTopicDto[];
    countries: App.DTO.CountryDto[];
    projects: App.DTO.ProjectDto[];
}

const PRIORITY_OPTIONS = [
    { value: "low", label: "Low" },
    { value: "middle", label: "Middle" },
    { value: "high", label: "High" },
] as const;

export default function CreateTicketPage({ categories, topics, countries, projects }: Props) {
    const [categoryId, setCategoryId] = useState("");
    const [topicId, setTopicId] = useState("");

    // Берём selectedTopic по выбранному topicId
    const selectedTopic = useMemo(() => {
        return topics.find((t) => t.id.toString() === topicId);
    }, [topicId, topics]);

    // Фильтруем список тем по выбранной категории
    const filteredTopics = useMemo(() => {
        return topics.filter((t) => t.category_id.toString() === categoryId);
    }, [categoryId, topics]);

    // Формируем Zod-схему: базовые поля + динамические из selectedTopic
    const formSchema = useMemo(() => {
        const base = z.object({
            category_id: z.string().min(1, "Category is required"),
            topic_id: z.string().min(1, "Topic is required"),
            priority: z.string().min(1, "Priority is required"),
        });

        if (!selectedTopic) return base;

        // Собираем все динамические поля в виде { "23": ZodType, "42": ZodType, ... }
        const dynamicFields = selectedTopic.fields?.reduce((acc, field) => {
            acc[field.id.toString()] = generateZodSchemaFromRules(
                field.validation_rules ?? [],
                field.is_required,
                field.type
            );
            return acc;
        }, {} as Record<string, z.ZodTypeAny>);


        return base.extend({
            priority: z.enum(["low", "middle", "high"], {
                required_error: "Priority is required",
            }),
            // Расширяем базовую схему динамическими
            ...dynamicFields,
        });
    }, [selectedTopic]);

    // Инициализируем react-hook-form
    const form = useForm({
        resolver: zodResolver(formSchema),
        defaultValues: {
            category_id: "",
            topic_id: "",
            priority: "low",
        },
    });

    // Получаем экземпляр api из нашего хука
    const api = useApi();

    // onSubmit: преобразование данных + запрос на сервер
    const onSubmit = async (data: any) => {
        const { category_id, topic_id, priority } = data;

        // Определяем, есть ли среди полей хотя бы один файл
        let hasFile = false;
        if (selectedTopic?.fields) {
            for (const fieldDef of selectedTopic.fields) {
                const val = data[fieldDef.id.toString()];
                if (
                    val instanceof File ||
                    (Array.isArray(val) && val.some((f) => f instanceof File))
                ) {
                    hasFile = true;
                    break;
                }
            }
        }

        if (hasFile) {
            const formData = new FormData();
            formData.append("category_id", category_id);
            formData.append("topic_id", topic_id);
            formData.append("priority", priority);

            if (selectedTopic?.fields) {
                for (const fieldDef of selectedTopic.fields) {
                    const key = fieldDef.id.toString();                  // "23"
                    const formKey = `fields[field_${key}]`;             // "fields[field_23]"
                    const value = data[key];

                    if (value instanceof File) {
                        // одиночный файл
                        formData.append(formKey, value);
                    } else if (Array.isArray(value) && value.every((f) => f instanceof File)) {
                        // массив файлов
                        value.forEach((fileObj: File) => {
                            formData.append(formKey + "[]", fileObj);
                        });
                    } else {
                        // строка/число/и т.д.
                        formData.append(formKey, String(value));
                    }
                }
            }

            try {
                await api.post(
                    route("tickets.create"),
                    formData,
                    {
                        onSuccess: () => {
                            toast.success("Ticket created successfully");
                            router.visit(route("tickets.my.show"));
                            form.reset();
                        },
                        onError: (error) => {
                            const message = error || "Unknown error";
                            form.setError("root", { message });
                            toast.error("Error creating ticket: " + message);
                        },
                    }
                );
            } catch (e) {
                const message = (e as Error).message || "Unknown error";
                form.setError("root", { message });
                toast.error("Error creating ticket: " + message);
            }
        } else {
            // Без файлов — можно отправить JSON
            const fields: Record<string, any> = {};
            if (selectedTopic?.fields) {
                for (const fieldDef of selectedTopic.fields) {
                    const key = fieldDef.id.toString();
                    fields[`field_${key}`] = data[key];
                }
            }

            const payload = {
                category_id,
                topic_id,
                priority,
                fields,
            };

            try {
                await api.post(
                    route("tickets.create"),
                    payload,
                    {
                        requestName: "createTicket",
                        onSuccess: ( data) => {
                            toast.success("Ticket created successfully");
                            router.visit(route("tickets.my.show", {currentTicketId: data.ticket.id}));
                            form.reset();
                        },
                        onError: (error) => {
                            const message = error || "Unknown error";
                            form.setError("root", { message });
                            toast.error("Error creating ticket: " + message);
                        },
                    }
                );
            } catch (e) {
                const message = (e as Error).message || "Unknown error";
                form.setError("root", { message });
                toast.error("Error creating ticket: " + message);
            }
        }
    };

    return (
        <AppLayout>
            <Head title="Create Ticket" />
            <div className="mx-auto w-full max-w-xl py-10">
                <Card>
                    <CardHeader>
                        <CardTitle>Create Ticket</CardTitle>
                        <CardDescription>Fill in the details below</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form key={topicId || "no-topic"} {...form}>
                            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                                {/** Category */}
                                <FormField
                                    control={form.control}
                                    name="category_id"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Category</FormLabel>
                                            <FormControl>
                                                <Select
                                                    onValueChange={(val) => {
                                                        field.onChange(val);
                                                        setCategoryId(val);
                                                        setTopicId("");
                                                        // Сбрасываем выбранный topic_id в форме
                                                        form.setValue("topic_id", "");
                                                    }}
                                                    value={field.value}
                                                >
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="Select Category" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {categories.map((cat) => (
                                                            <SelectItem
                                                                key={cat.id}
                                                                value={cat.id.toString()}
                                                            >
                                                                {cat.name}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />

                                {/** Topic */}
                                <FormField
                                    control={form.control}
                                    name="topic_id"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Topic</FormLabel>
                                            <FormControl>
                                                <Select
                                                    onValueChange={(val) => {
                                                        field.onChange(val);
                                                        setTopicId(val);
                                                    }}
                                                    value={field.value}
                                                    disabled={!categoryId}
                                                >
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="Select Topic" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {filteredTopics.map((topic) => (
                                                            <SelectItem
                                                                key={topic.id}
                                                                value={topic.id.toString()}
                                                            >
                                                                {topic.name}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />

                                {/** Priority */}
                                {selectedTopic && (
                                    <FormField
                                        control={form.control}
                                        name="priority"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel>Priority</FormLabel>
                                                <FormControl>
                                                    <Select
                                                        onValueChange={field.onChange}
                                                        value={field.value}
                                                    >
                                                        <SelectTrigger className="w-full">
                                                            <SelectValue placeholder="Select Priority" />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {PRIORITY_OPTIONS.map((opt) => (
                                                                <SelectItem
                                                                    key={opt.value}
                                                                    value={opt.value}
                                                                >
                                                                    {opt.label}
                                                                </SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                </FormControl>
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />
                                )}

                                {/** Динамические поля */}
                                {selectedTopic && selectedTopic.fields && selectedTopic.fields.length > 0 && (
                                    <DynamicTicketFields
                                        fields={selectedTopic.fields}
                                        control={form.control}
                                        errors={form.formState.errors}
                                        countryOptions={countries}
                                        projectOptions={projects}
                                    />
                                )}

                                <div className="flex justify-end">
                                    <Button
                                        className={cn(
                                                api.isLoading.request('createTicket') && "pointer-events-none opacity-50",
                                            )}
                                        type="submit"
                                    >
                                        {api.isLoading.request('createTicket') && <LoaderCircle className="animate-spin" />}
                                        Submit
                                    </Button>
                                </div>
                            </form>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
