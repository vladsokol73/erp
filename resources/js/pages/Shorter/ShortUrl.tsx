import React, { useState } from "react";
import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import { z } from "zod";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";
import { route } from "ziggy-js";
import useApi from "@/hooks/use-api";
import { ShortLinkCard } from "@/components/shorter/short-link-card";

const formSchema = z.object({
    original_url: z.string().url("Must be a valid URL"),
    short_code: z.string().max(6).optional().or(z.literal("")),
    domain: z.string().min(1),
});

type FormData = z.infer<typeof formSchema>;

interface Props {
    domains: App.DTO.Shorter.DomainDto[];
}

export default function ShortUrlCreatePage({ domains }: Props) {
    const api = useApi();
    const [shortUrl, setShortUrl] = useState<string | null>(null);
    const [originalUrl, setOriginalUrl] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);

    const form = useForm<FormData>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            original_url: "",
            short_code: "",
            domain: domains[0]?.domain ?? "",
        },
    });

    const [serverError, setServerError] = useState<string | null>(null);

    const onSubmit = (data: FormData) => {
        setServerError(null);
        setShortUrl(null);
        setLoading(true);

        setOriginalUrl(data.original_url);

        api.post(route("shorter.url.create"), data, {
            onSuccess: (data) => {
                const short = data.message;
                setShortUrl(short);
                setLoading(false);

                form.reset({
                    original_url: "",
                    short_code: "",
                    domain: domains[0]?.domain ?? "",
                });
            },
            onError: (error) => {
                setLoading(false);

                if (error) {
                    setServerError(error);
                } else {
                    setServerError("Unknown error");
                }

            },
        });
    };

    return (
        <AppLayout>
            <Head title="Create Short URL" />
            <div className="mx-auto w-full max-w-2xl space-y-6 pt-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Create Short URL</CardTitle>
                        <CardDescription>Create a short URL and share it with others</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form {...form}>
                            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                                <FormField
                                    control={form.control}
                                    name="original_url"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Original URL</FormLabel>
                                            <FormControl>
                                                <Input placeholder="Original URL" {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="short_code"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Short Code</FormLabel>
                                            <FormControl>
                                                <Input placeholder="Short Code (6 characters)" {...field} />
                                            </FormControl>
                                            <p className="text-sm text-muted-foreground">
                                                Leave empty for random code generation
                                            </p>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="domain"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Select domain</FormLabel>
                                            <FormControl>
                                                <Select value={field.value} onValueChange={field.onChange}>
                                                    <SelectTrigger className="w-full">
                                                        <SelectValue placeholder="Select domain" />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        {domains.map((d) => (
                                                            <SelectItem key={d.id} value={d.domain}>
                                                                {d.domain}
                                                            </SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <div className="flex justify-end">
                                    <Button type="submit">Create</Button>
                                </div>
                            </form>
                        </Form>
                    </CardContent>
                </Card>

                {/* Карточка появляется сразу после submit */}
                {originalUrl && (
                    <ShortLinkCard
                        originalUrl={originalUrl}
                        shortUrl={shortUrl}
                        loading={loading}
                        error={serverError}
                    />
                )}
            </div>
        </AppLayout>
    );
}
