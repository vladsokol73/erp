"use client"

import { useState } from "react"
import { Head, router, usePage } from "@inertiajs/react"
import { useRoute } from "ziggy-js"
import { z } from "zod"
import { useForm } from "react-hook-form"
import { zodResolver } from "@hookform/resolvers/zod"

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"

import LoginLayout from "@/components/layouts/login-layout"
import useApi from "@/hooks/use-api"

// ---------------- Zod схема ----------------
const schema = z.object({
    password: z
        .string()
        .min(8, { message: "Password must be at least 8 characters long." })
        .regex(/[A-Za-z]/, { message: "Password must contain letters." })
        .regex(/\d/, { message: "Password must contain numbers." }),
    password_confirmation: z
        .string()
        .min(1, { message: "Please confirm your new password." }),
}).refine((data) => data.password === data.password_confirmation, {
    message: "Passwords do not match.",
    path: ["password_confirmation"],
})

export type ForcePasswordFormValues = z.infer<typeof schema>

export default function ForcePasswordChange() {
    const route = useRoute()
    const { props } = usePage<{ userEmail?: string }>()
    const userEmail = props?.userEmail ?? ""

    const api = useApi()

    const [isSaving, setIsSaving] = useState(false)
    const [serverMessage, setServerMessage] = useState<string | null>(null)

    const form = useForm<ForcePasswordFormValues>({
        resolver: zodResolver(schema),
        defaultValues: {
            password: "",
            password_confirmation: "",
        },
        mode: "onTouched",
    })

    // ---------- Сабмит ----------
    const onSubmit = async (values: ForcePasswordFormValues) => {
        setIsSaving(true)
        setServerMessage(null)

        await api.post(route("password.force.update"), values, {
            onSuccess: () => {
                setIsSaving(false)
                setServerMessage("Password has been changed successfully.")

                router.visit(route("home"))
            },
            onError: (error) => {
                setIsSaving(false)

                const errors = (error as any)?.errors
                if (errors && typeof errors === "object") {
                    const firstField = Object.keys(errors)[0]
                    const firstMsg = Array.isArray(errors[firstField])
                        ? errors[firstField][0]
                        : String(errors[firstField])
                    setServerMessage(firstMsg || "Failed to change password.")
                    return
                }

                setServerMessage(
                    typeof (error as any)?.message === "string"
                        ? (error as any).message
                        : "Failed to change password."
                )
            },
        })
    }

    return (
        <LoginLayout>
            <Head title="Change Password" />
            <div className="flex flex-col gap-6 w-full max-w-xl mx-auto">
                <Card className="md:px-6 md:py-12">
                    <CardHeader className="text-center mb-4">
                        <CardTitle className="text-xl">Change your password</CardTitle>
                        <CardDescription>
                            For security reasons you must set a new password to continue
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <Form {...form}>
                            <form
                                onSubmit={form.handleSubmit(onSubmit)}
                                className="flex flex-col gap-4"
                            >
                                {/* Email информативно */}
                                {userEmail && (
                                    <div className="space-y-2">
                                        <FormLabel>Email</FormLabel>
                                        <Input value={userEmail} disabled />
                                    </div>
                                )}

                                <FormField
                                    control={form.control}
                                    name="password"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>New password</FormLabel>
                                            <FormControl>
                                                <Input
                                                    type="password"
                                                    autoComplete="new-password"
                                                    placeholder="Enter new password"
                                                    {...field}
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="password_confirmation"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Confirm password</FormLabel>
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

                                <Button type="submit" className="w-full mt-2" disabled={isSaving}>
                                    {isSaving ? "Updating..." : "Update password"}
                                </Button>
                            </form>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </LoginLayout>
    )
}
