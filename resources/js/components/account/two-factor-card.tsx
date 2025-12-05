"use client";

import * as React from "react";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";

import { Shield, Copy } from "lucide-react";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogClose,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";
import { confirm } from "@/components/ui/confirmer";

function toImageSrc(qr: string | null): string | null {
    if (!qr) return null;
    const t = qr.trim();
    if (t.startsWith("data:") || /^https?:\/\//i.test(t)) return t;
    if (t.startsWith("<svg") || t.startsWith("<?xml")) {
        return `data:image/svg+xml;utf8,${encodeURIComponent(t)}`;
    }
    return t;
}

// Валидация 6-значного кода
const codeSchema = z.object({
    code: z.string().regex(/^\d{6}$/, { message: "Enter a 6 digit code." }),
});
type CodeFormValues = z.infer<typeof codeSchema>;

const ROUTES = {
    generate: "2fa.generate",
    confirm: "2fa.confirm",
    disable: "2fa.disable",
} as const;

interface TwoFactorCardProps {
    initialEnabled: boolean;
    className?: string;
}

export function TwoFactorCard({
                                  initialEnabled,
                                  className,
                              }: TwoFactorCardProps) {
    const api = useApi();
    const [enabled, setEnabled] = React.useState(initialEnabled);
    const [loading, setLoading] = React.useState(false);

    const [message, setMessage] = React.useState<{
        type: "success" | "error";
        text: string;
    } | null>(null);

    const [open, setOpen] = React.useState(false);
    const [secret, setSecret] = React.useState<string | null>(null);
    const [qrCode, setQrCode] = React.useState<string | null>(null);

    const form = useForm<CodeFormValues>({
        resolver: zodResolver(codeSchema),
        defaultValues: { code: "" },
        mode: "onTouched",
    });

    // Мемоизация src для QR
    const qrSrc = React.useMemo(() => toImageSrc(qrCode), [qrCode]);

    const setSuccess = (text: string, autoClear = false) => {
        setMessage({ type: "success", text });
        if (autoClear) {
            window.setTimeout(() => setMessage(null), 2000);
        }
    };
    const setError = (text: string) => setMessage({ type: "error", text });

    // Полная очистка данных диалога
    const resetDialogState = () => {
        form.reset();
        setSecret(null);
        setQrCode(null);
    };

    // Открыть диалог и запросить секрет/QR
    const openEnableDialog = async () => {
        setOpen(true);
        setMessage(null);
        resetDialogState();

        setLoading(true);
        await api.get(route(ROUTES.generate), {
            onSuccess: (data) => {
                setSecret(data?.secret ?? null);
                setQrCode(data?.qrCode ?? null);
            },
            onError: (error) => {
                setError((error as any)?.message ?? "Failed to generate 2FA secret.");
            },
            onFinally: () => setLoading(false),
        });
    };

    // Подтверждение кода из диалога
    const onConfirm = form.handleSubmit(async (values) => {
        if (!secret) return;
        setMessage(null);

        await api.post(
            route(ROUTES.confirm),
            { code: values.code, secret },
            {
                onSuccess: () => {
                    setEnabled(true);
                    setSuccess("Two-factor authentication enabled.", true);
                    setOpen(false);
                    resetDialogState();
                },
                onError: (error) => {
                    const err = (error as any) ?? {};
                    const errors = err.errors;
                    if (errors?.code) {
                        const msg = Array.isArray(errors.code)
                            ? errors.code[0]
                            : String(errors.code);
                        form.setError("code", { type: "server", message: msg });
                    } else {
                        setError("Invalid confirmation code.");
                    }
                },
            }
        );
    });

    // Отключение 2FA
    const handleDisable = async () => {
        const confirmed = await confirm({
            title: "Are you absolutely sure?",
            description: "This will disable two-factor authentication for your account.",
            actionText: "Disable",
            cancelText: "Cancel",
        });

        if (!confirmed) return;

        setLoading(true);
        setMessage(null);

        await api.post(
            route(ROUTES.disable),
            {},
            {
                onSuccess: () => {
                    setEnabled(false);
                    setSuccess("Two-factor authentication disabled.", true);
                },
                onError: (error) => {
                    setError(
                        (error as any)?.message ??
                        "Failed to disable two-factor authentication.",
                    );
                },
                onFinally: () => setLoading(false),
            },
        );
    };

    // Копирование секрета
    const copySecret = async () => {
        if (!secret) return;
        try {
            await navigator.clipboard.writeText(secret);
            setSuccess("Secret copied to clipboard.", true);
        } catch {
            setError("Failed to copy secret.");
        }
    };

    return (
        <Card className={className}>
            <CardHeader className="flex items-center justify-between space-y-0">
                <div className="space-y-1">
                    <CardTitle className="flex items-center gap-2">
                        <Shield className="h-5 w-5" />
                        Secure Your Account
                    </CardTitle>
                    <CardDescription>
                        Two‑factor authentication adds an extra layer of security. To log in
                        you&apos;ll also need a 6‑digit code.
                    </CardDescription>
                </div>

                {enabled ? (
                    <Button
                        variant="secondary"
                        size="sm"
                        onClick={handleDisable}
                        disabled={loading}
                    >
                        Disable
                    </Button>
                ) : (
                    <Button size="sm" onClick={openEnableDialog} disabled={loading}>
                        Enable
                    </Button>
                )}
            </CardHeader>

            {message && !open && (
                <CardContent className="pt-0">
                    <p className={message.type === "success" ? "text-xs text-green-600" : "text-xs text-red-500"}>
                        {message.text}
                    </p>
                </CardContent>
            )}

            <Dialog
                open={open}
                onOpenChange={(v) => {
                    setOpen(v);
                    if (!v) {
                        // закрываем — чистим состояние диалога
                        resetDialogState();
                        setMessage(null);
                    }
                }}
            >
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Enable Two‑Factor Authentication</DialogTitle>
                        <DialogDescription>
                            Scan the QR code with your authenticator app or enter the secret
                            manually, then confirm with a 6‑digit code.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="flex flex-col gap-4">
                        {/* QR */}
                        {qrSrc && (
                            <div className="mx-auto">
                                <img
                                    src={qrSrc}
                                    alt="2FA QR Code"
                                    className="p-2 size-44 rounded-md border bg-background"
                                />
                            </div>
                        )}

                        <div className="space-y-2">
                            <Label htmlFor="twofa-secret">Secret (manual setup)</Label>
                            <div className="flex items-center gap-2">
                                <Input id="twofa-secret" readOnly value={secret ?? ""} />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    onClick={copySecret}
                                    disabled={!secret}
                                    aria-label="Copy secret"
                                >
                                    <Copy className="h-4 w-4" />
                                </Button>
                            </div>
                            <p className="text-xs text-muted-foreground">
                                Use this secret if you can’t scan the QR code.
                            </p>
                        </div>

                        <Form {...form}>
                            <form onSubmit={onConfirm} className="space-y-3">
                                <FormField
                                    control={form.control}
                                    name="code"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Confirmation Code</FormLabel>
                                            <FormControl>
                                                <Input
                                                    {...field}
                                                    inputMode="numeric"
                                                    autoComplete="one-time-code"
                                                    maxLength={6}
                                                    placeholder="000000"
                                                />
                                            </FormControl>
                                            <FormMessage />
                                        </FormItem>
                                    )}
                                />

                                {message && open && (
                                    <p className={message.type === "success" ? "text-xs text-green-600" : "text-xs text-red-500"}>
                                        {message.text}
                                    </p>
                                )}

                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            disabled={loading || form.formState.isSubmitting}
                                        >
                                            Cancel
                                        </Button>
                                    </DialogClose>

                                    <Button
                                        type="submit"
                                        disabled={
                                            loading || form.formState.isSubmitting || !secret
                                        }
                                    >
                                        {form.formState.isSubmitting ? "Confirming..." : "Confirm"}
                                    </Button>
                                </DialogFooter>
                            </form>
                        </Form>
                    </div>
                </DialogContent>
            </Dialog>
        </Card>
    );
}
