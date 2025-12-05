import AppLayout from "@/components/layouts/app-layout";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { TagSelector } from "@/components/common/tag-selector";
import { Dropzone } from "@/components/common/dropzone";

import { route } from "ziggy-js";
import useApi from "@/hooks/use-api";
import { Head } from "@inertiajs/react";
import { toast } from "sonner"
import { router } from '@inertiajs/react'
import { CountryField } from "@/components/ticket/fields/country-field";

// Схема валидации
const formSchema = z.object({
    country_id: z.number().min(1, "Country is required"),
    tags: z.array(z.number()).min(1, "At least one tag is required"),
    files: z.array(z.any()).min(1, "Please upload at least one file"),
});

type FormData = z.infer<typeof formSchema>;

interface Props {
    tags: App.DTO.Creative.TagDto[];
    countries: App.DTO.CountryDto[];
}

export default function Page({ tags, countries }: Props) {
    const api = useApi();
    const form = useForm<FormData>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            country_id: 0,
            tags: [],
            files: [],
        },
    });

    const onSubmit = (data: FormData) => {
        api.post(route("creatives.new_creative.create"), data, {
            onSuccess: () => {
                toast("Creative created successfully");
                router.visit(route("creatives.library.show"));
                form.reset();
            },
        });
    };

    const onDeleteFile = async ({
                                    file,
                                    response,
                                }: {
        file: File;
        response?: any;
    }) => {
        if (!response?.data?.url) return;

        await api.post(
            route("creatives.new_creative.delete"),
            { url: response.data.url },
            {
                onSuccess: () => {
                    form.setValue(
                        "files",
                        form
                            .getValues("files")
                            .filter((f) => f.url !== response.data.url)
                    );
                },
            }
        );
    };

    const onUploaded = ({
                            file,
                            response,
                        }: {
        file: File;
        response: any;
    }) => {
        if (!response?.data) return;

        const current = form.getValues("files");
        form.setValue("files", [...current, response.data]);
    };

    return (
        <AppLayout>
            <Head title="New Creative" />

            <div className="mx-auto w-full max-w-2xl lg:w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>New Creative</CardTitle>
                        <CardDescription>Creative creation form</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form {...form}>
                            <form
                                onSubmit={form.handleSubmit(onSubmit)}
                                className="space-y-4"
                            >
                                {/* Dropzone */}
                                <Dropzone
                                    uploadUrl={route("creatives.new_creative.upload")}
                                    accept={{ "image/*": [], "video/*": [] }}
                                    maxFiles={10}
                                    onRemove={onDeleteFile}
                                    onUploaded={onUploaded}
                                />

                                {/* FilesData errors */}
                                <FormField
                                    control={form.control}
                                    name="files"
                                    render={() => <FormMessage />}
                                />

                                {/* Country */}
                                <FormField
                                    control={form.control}
                                    name="country_id"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Country</FormLabel>
                                            <FormControl>
                                                <CountryField
                                                    value={field.value}
                                                    onChange={field.onChange}
                                                    options={countries}
                                                    placeholder="Select country"
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />


                                {/* Tags */}
                                <FormField
                                    control={form.control}
                                    name="tags"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Tags</FormLabel>
                                            <FormControl>
                                                <TagSelector
                                                    allTags={tags.map((tag) => ({
                                                        id: tag.id,
                                                        name: tag.name,
                                                        style: tag.style ?? "gray",
                                                    }))}
                                                    selectedIds={field.value}
                                                    onChange={field.onChange}
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />

                                {/* Submit */}
                                <div className="flex justify-end">
                                    <Button type="submit">Create</Button>
                                </div>
                            </form>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
