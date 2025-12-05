"use client";

import * as React from "react";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogClose } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Copy, Send } from "lucide-react";
import { confirm } from "@/components/ui/confirmer";

const ROUTES = {
    link: "account.telegram.link",
    destroy: "account.telegram.destroy",
    check: "account.telegram.check",
} as const;

type Msg = { type: "success" | "error"; text: string } | null;

interface TelegramConnectCardProps {
    initialConnected: boolean;
    className?: string;
}

export function TelegramConnectCard({ initialConnected, className }: TelegramConnectCardProps) {
    const api = useApi();

    const [connected, setConnected] = React.useState(initialConnected);
    const [loading, setLoading] = React.useState(false);
    const [message, setMessage] = React.useState<Msg>(null);

    const [open, setOpen] = React.useState(false);
    const [tgLink, setTgLink] = React.useState<string>("");

    const [isChecking, setIsChecking] = React.useState(false);
    const pollRef = React.useRef<number | null>(null);

    const setSuccess = (text: string, autoclear = false) => {
        setMessage({ type: "success", text });
        if (autoclear) setTimeout(() => setMessage(null), 1800);
    };
    const setError = (text: string) => setMessage({ type: "error", text });

    const openDialog = async () => {
        setOpen(true);
        setMessage(null);
        setTgLink("");
        setLoading(true);

        await api.get(route(ROUTES.link), {
            onSuccess: (data) => {
                setTgLink(data?.link ?? "");
            },
            onError: (e) => {
                setError((e as any)?.message ?? "Failed to get Telegram link.");
                setOpen(false);
            },
            onFinally: () => setLoading(false),
        });
    };

    const handleDisable = async () => {
        const confirmed = await confirm({
            title: "Are you absolutely sure?",
            description: "This will disconnect Telegram notifications from your account.",
            actionText: "Disable",
            cancelText: "Cancel",
        });

        if (!confirmed) return;

        setLoading(true);
        setMessage(null);

        await api.delete(route(ROUTES.destroy), {
            onSuccess: () => {
                setConnected(false);
                setSuccess("Telegram notifications disconnected.", true);
            },
            onError: (e) =>
                setError((e as any)?.message ?? "Failed to disconnect Telegram."),
            onFinally: () => setLoading(false),
        });
    };

    const checkOnce = React.useCallback(async () => {
        await api.get(route(ROUTES.check), {
            onSuccess: () => {
                // Успех: останавливаем поллинг, закрываем диалог, помечаем connected
                stopPolling();
                setConnected(true);
                setSuccess("Telegram connected.", true);
                setOpen(false);
            },
            onError: () => {
                /* ждём следующую попытку */
            },
        });
    }, [api]);

    const startPolling = React.useCallback(() => {
        // Защита от мульти‑стартов
        if (pollRef.current !== null) return;
        setIsChecking(true);

        void checkOnce();

        const id = window.setInterval(() => {
            void checkOnce();
        }, 5000);

        pollRef.current = id;
    }, [checkOnce]);

    const stopPolling = React.useCallback(() => {
        if (pollRef.current !== null) {
            window.clearInterval(pollRef.current);
            pollRef.current = null;
        }
        setIsChecking(false);
    }, []);

    React.useEffect(() => () => stopPolling(), [stopPolling]);

    const copyLink = async () => {
        if (!tgLink) return;
        try {
            await navigator.clipboard.writeText(tgLink);
            setSuccess("Link copied to clipboard.", true);

            startPolling();
        } catch {
            setError("Failed to copy link.");
        }
    };

    const openTgLink = () => {
        if (!tgLink) return;
        window.open(tgLink, "_blank", "noopener,noreferrer");

        startPolling();
    };

    return (
        <Card className={className}>
            <CardHeader className="flex items-center justify-between space-y-0">
                <div className="space-y-1">
                    <CardTitle className="flex items-center gap-2">
                        <Send className="h-5 w-5" />
                        Connect Notifications
                    </CardTitle>
                    <CardDescription>
                        Link your Telegram to receive notifications.
                    </CardDescription>
                </div>

                {connected ? (
                    <Button variant="secondary" size="sm" onClick={handleDisable} disabled={loading}>
                        Disable
                    </Button>
                ) : (
                    <Button size="sm" onClick={openDialog} disabled={loading}>
                        Connect
                    </Button>
                )}
            </CardHeader>

            {/* Сообщение показываем в карточке, только когда диалог закрыт */}
            {message && !open && (
                <CardContent className="pt-0">
                    <p className={message.type === "success" ? "text-xs text-green-600" : "text-xs text-red-500"}>{message.text}</p>
                </CardContent>
            )}

            {/* Диалог подключения */}
            <Dialog
                open={open}
                onOpenChange={(v) => {
                    setOpen(v);
                    if (!v) setMessage(null); // чистим сообщение при закрытии
                }}
            >
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Connect Telegram</DialogTitle>
                        <DialogDescription>
                            Open the link below and press <span className="font-medium">Start</span> in Telegram to link your account.
                        </DialogDescription>
                    </DialogHeader>

                    {/* сообщение внутри диалога */}
                    {message && (
                        <p className={message.type === "success" ? "text-xs text-green-600" : "text-xs text-red-500"}>
                            {message.text}
                        </p>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="tg-link">Telegram link</Label>
                        <div className="flex items-center gap-2">
                            <Input id="tg-link" readOnly value={tgLink} />
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                onClick={copyLink}
                                disabled={!tgLink || loading || isChecking}
                                aria-label="Copy link"
                            >
                                <Copy className="h-4 w-4" />
                            </Button>
                        </div>
                        <p className="text-xs text-muted-foreground">If the link does not open, copy it and open manually in Telegram.</p>
                    </div>

                    <DialogFooter className="gap-2">
                        <DialogClose asChild>
                            <Button type="button" variant="ghost" disabled={loading}>
                                Cancel
                            </Button>
                        </DialogClose>

                        <Button onClick={openTgLink} disabled={!tgLink || loading || isChecking}>
                            Open in Telegram
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </Card>
    );
}
