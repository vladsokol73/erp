"use client";

import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";

// Zod-схема валидации
const passwordSchema = z.object({
    current_password: z.string().min(1, { message: "Current password is required." }),
    new_password: z
        .string()
        .min(8, { message: "New password must be at least 8 characters." })
        .regex(/[A-Za-z]/, { message: "Password must contain letters." })
        .regex(/\d/, { message: "Password must contain numbers." }),
    new_password_confirmation: z.string().min(1, { message: "Please confirm your new password." }),
});

export type PasswordFormValues = z.infer<typeof passwordSchema>;

interface ChangePasswordCardProps {
    onSubmit: (values: PasswordFormValues) => void;
    isSaving: boolean;
    serverMessage?: string | null;
}

export function ChangePasswordCard({
                                       onSubmit,
                                       isSaving,
                                       serverMessage,
                                   }: ChangePasswordCardProps) {
    const form = useForm<PasswordFormValues>({
        resolver: zodResolver(passwordSchema),
        defaultValues: {
            current_password: "",
            new_password: "",
            new_password_confirmation: "",
        },
        mode: "onTouched",
    });

    return (
        <Card>
            <CardHeader>
                <CardTitle>Change Password</CardTitle>
                <CardDescription>Update your account password for better security</CardDescription>
            </CardHeader>
            <CardContent>
                <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                        <FormField
                            control={form.control}
                            name="current_password"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Current Password</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="password"
                                            autoComplete="current-password"
                                            placeholder="Enter current password"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        <FormField
                            control={form.control}
                            name="new_password"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>New Password</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="password"
                                            autoComplete="new-password"
                                            placeholder="Enter new password"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormDescription>At least 8 characters.</FormDescription>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        <FormField
                            control={form.control}
                            name="new_password_confirmation"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Confirm Password</FormLabel>
                                    <FormControl>
                                        <Input
                                            type="password"
                                            autoComplete="new-password"
                                            placeholder="Repeat new password"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {serverMessage && (
                            <p
                                className={
                                    serverMessage.includes("successfully")
                                        ? "text-xs text-green-600"
                                        : "text-xs text-red-500"
                                }
                            >
                                {serverMessage}
                            </p>
                        )}

                        <Button type="submit" className="w-full" disabled={isSaving}>
                            {isSaving ? "Saving..." : "Change Password"}
                        </Button>
                    </form>
                </Form>
            </CardContent>
        </Card>
    );
}
