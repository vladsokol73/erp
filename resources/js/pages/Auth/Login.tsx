"use client"

import { useState } from "react"
import { useRoute } from "ziggy-js"
import {Head, router} from "@inertiajs/react"
import { zodResolver } from "@hookform/resolvers/zod"
import { useForm } from "react-hook-form"
import { z } from "zod"
import axios, { AxiosResponse } from "axios"

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
import { toast } from "sonner"
import LoginLayout from "@/components/layouts/login-layout"
import TwoFactorAuthDialog from "@/pages/Auth/TwoFactorAuthDialog"

// Validation schema for login form
const loginFormSchema = z.object({
    email: z.string().email({
        message: "Please enter a valid email address.",
    }),
    password: z.string().min(6, {
        message: "Password must be at least 6 characters long.",
    }),
})

// Types for forms and responses
interface LoginResponse {
    success: boolean
    requires2fa?: boolean
    message?: string
}

export default function Login() {
    const route = useRoute()
    const params = new URLSearchParams(typeof window !== 'undefined' ? window.location.search : '')
    const redirectParam = params.get('redirect')
    const redirect = redirectParam ? decodeURIComponent(redirectParam) : null

    // State for 2FA dialog
    const [is2FAOpen, setIs2FAOpen] = useState(false)

    // Initialize form with react-hook-form and zod
    const form = useForm<z.infer<typeof loginFormSchema>>({
        resolver: zodResolver(loginFormSchema),
        defaultValues: {
            email: "",
            password: "",
        },
    })

    // Login form submission handler
    const onSubmit = async (data: z.infer<typeof loginFormSchema>) => {
        try {
            const response: AxiosResponse<LoginResponse> = await axios.post(
                route('login.submit'),
                data
            )

            if (response.data.success) {
                if (redirect) {
                    window.location.assign(redirect)
                } else {
                router.visit(route('home'))
                }
            } else {
                if (response.data.requires2fa) {
                    setIs2FAOpen(true)
                } else {
                    form.setError("email", {
                        type: "manual",
                        message: response.data.message || "Invalid credentials"
                    })
                    form.setError("password", {
                        type: "manual",
                        message: " "
                    })
                }
            }
        } catch (error) {
            toast("An error occurred while signing in. Please try again.")
        }
    }

    // Forgot password handler
    const handleForgotPassword = () => {
        toast("Please contact your manager!")
    }

    return (
        <LoginLayout>
            <Head title="Login" />
            <div className="flex flex-col gap-6 w-full max-w-xl mx-auto">
                <Card className="md:px-6 md:py-20">
                    <CardHeader className="text-center mb-4">
                        <CardTitle className="text-xl">Sign In</CardTitle>
                        <CardDescription>
                            Enter your email and password to login
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form {...form}>
                            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                                <FormField
                                    control={form.control}
                                    name="email"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Email</FormLabel>
                                            <FormControl>
                                                <Input
                                                    placeholder="name@example.com"
                                                    type="email"
                                                    {...field}
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <FormField
                                    control={form.control}
                                    name="password"
                                    render={({ field }) => (
                                        <FormItem>
                                            <div className="flex items-center justify-between">
                                                <FormLabel>Password</FormLabel>
                                                <button
                                                    type="button"
                                                    onClick={handleForgotPassword}
                                                    className="text-sm text-primary hover:underline"
                                                >
                                                    Forgot Password?
                                                </button>
                                            </div>
                                            <FormControl>
                                                <Input type="password" {...field} />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />
                                <Button type="submit" className="w-full mt-2">
                                    Sign In
                                </Button>
                            </form>
                        </Form>
                    </CardContent>
                </Card>
            </div>

            <TwoFactorAuthDialog
                isOpen={is2FAOpen}
                onOpenChange={setIs2FAOpen}
            />
        </LoginLayout>
    )
}
