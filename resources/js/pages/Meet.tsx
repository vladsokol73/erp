"use client";

import React, { useState } from "react";
import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent } from "@/components/ui/card";
import { Video, Calendar, Copy, ExternalLink } from "lucide-react";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";
import { toast } from "sonner";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from "@/components/ui/dialog";
import { Table, TableBody, TableRow, TableCell } from "@/components/ui/table";
import DateFormatter from "@/components/common/date-formatter";
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
} from "@/components/ui/dropdown-menu";

interface Props {
    rooms: App.DTO.Meet.MeetRoomDto[];
    ttl_seconds: number;
}

export default function MeetPage({ rooms, ttl_seconds }: Props) {
    const api = useApi();
    const [roomCode, setRoomCode] = useState("");
    const [isCreatingRoom, setIsCreatingRoom] = useState(false);
    const [isJoiningRoom, setIsJoiningRoom] = useState(false);

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [createdLink, setCreatedLink] = useState("");

    const formatTtl = (s?: number | null) => {
        if (s == null) return "-";
        if (s <= 0) return "expired";
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return m > 0 ? `${m} min ${sec} sec` : `${sec} sec`;
    };

    // Получить публичную ссылку присоединения (join_url)
    const fetchPublicJoinUrl = async (room: string): Promise<string | null> => {
        return new Promise((resolve) => {
            api.get(route("meet.public.redirect", { room }), {
                onSuccess: (data) => {
                    const joinUrl = (data as any)?.join_url;
                    if (!joinUrl || typeof joinUrl !== "string") {
                        toast.error("Invalid response: join_url is missing");
                        resolve(null);
                        return;
                    }
                    resolve(joinUrl);
                },
                onError: (message) => {
                    toast.error(message || "Failed to get public link");
                    resolve(null);
                },
            });
        });
    };

    // Копирование ссылки комнаты.
    const copyRoomLink = async (
        room: string,
        linkType: "normal" | "public"
    ) => {
        try {
            let linkToCopy = "";

            if (linkType === "normal") {
                linkToCopy = route("meet.redirect", { room });
            } else {
                const publicUrl = await fetchPublicJoinUrl(room);
                if (!publicUrl) return; // Ошибка уже показана в toast
                linkToCopy = publicUrl;
            }

            await navigator.clipboard.writeText(linkToCopy);
            toast.success("Link copied");
        } catch {
            toast.error("Failed to copy link");
        }
    };

    // Создание новой комнаты
    const handleCreateRoom = async () => {
        setIsCreatingRoom(true);

        await api.post(
            route("meet.room.generate"),
            {},
            {
                onSuccess: (data) => {
                    if (data?.room) {
                        // По умолчанию показываем обычную ссылку на редирект (normal)
                        const link = route("meet.redirect", { room: data.room });
                        setCreatedLink(link);
                        setIsModalOpen(true);
                    } else {
                        toast.error("Failed to get room code");
                    }
                },
                onError: (message) => {
                    toast.error(message || "Failed to create room");
                },
                onFinally: () => {
                    setIsCreatingRoom(false);
                },
            }
        );
    };

    // Присоединение по введённому коду/ссылке
    const handleJoinRoom = async () => {
        const trimmedCode = roomCode.trim();

        if (!trimmedCode) {
            toast.error("Enter a meeting code");
            return;
        }

        setIsJoiningRoom(true);

        await api.get(route("meet.link", { room: trimmedCode }), {
            onSuccess: (data) => {
                if (data?.join_url) {
                    window.open(data.join_url, "_blank");
                } else {
                    toast.error("Failed to get join link");
                }
            },
            onError: (message) => {
                toast.error(message || "Room not found or expired");
            },
            onFinally: () => {
                setIsJoiningRoom(false);
            },
        });
    };

    // Копирование ссылки из модалки созданной комнаты
    const handleCopyLink = async () => {
        if (!createdLink) return;
        try {
            await navigator.clipboard.writeText(createdLink);
            toast.success("Link copied");
        } catch {
            toast.error("Failed to copy link");
        }
    };

    // Переход по ссылке из модалки
    const handleEnterCreated = () => {
        if (createdLink) {
            window.open(createdLink, "_blank");
        }
    };

    return (
        <AppLayout>
            <Head title="Video calls and meetings" />

            <div className="mx-auto w-full max-w-2xl space-y-6 pt-8">
                {/* Card for creating a new meeting */}
                <Card>
                    <CardContent className="p-6">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-4">
                                <div className="p-2 bg-primary/10 rounded-lg">
                                    <Video className="w-5 h-5 text-primary" />
                                </div>
                                <div>
                                    <h3 className="font-semibold text-foreground">New Meeting</h3>
                                    <p className="text-sm text-muted-foreground">
                                        Create a meeting to use now or later
                                    </p>
                                </div>
                            </div>
                            <Button
                                onClick={handleCreateRoom}
                                disabled={isCreatingRoom}
                                className="bg-primary hover:bg-primary/90"
                            >
                                {isCreatingRoom ? "Creating..." : "New Meeting"}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Card for joining a meeting */}
                <Card>
                    <CardContent className="p-6">
                        <div className="space-y-4">
                            <div className="flex items-center space-x-4">
                                <div className="p-2 bg-secondary/50 rounded-lg">
                                    <Calendar className="w-5 h-5 text-secondary-foreground" />
                                </div>
                                <div>
                                    <h3 className="font-semibold text-foreground">Join a Meeting</h3>
                                    <p className="text-sm text-muted-foreground">
                                        Enter a meeting code or link
                                    </p>
                                </div>
                            </div>

                            <div className="flex space-x-3">
                                <div className="flex-1">
                                    <Input
                                        type="text"
                                        placeholder="Enter meeting code or link"
                                        value={roomCode}
                                        onChange={(e) => setRoomCode(e.target.value)}
                                        className="w-full"
                                    />
                                </div>
                                <Button
                                    onClick={handleJoinRoom}
                                    disabled={isJoiningRoom || !roomCode.trim()}
                                    variant="outline"
                                >
                                    {isJoiningRoom ? "Joining..." : "Join"}
                                </Button>
                            </div>

                            {/* History of rooms (shadcn Table) */}
                            <div className="pt-4">
                                <div className="flex items-end justify-between mb-2">
                                    <h4 className="font-semibold">Your Rooms</h4>
                                    <p className="text-xs text-muted-foreground">
                                        A room expires in ~ {Math.max(1, Math.floor(ttl_seconds / 60))} min from creation
                                    </p>
                                </div>
                                {rooms && rooms.length > 0 ? (
                                    <div className="rounded-md border">
                                        <Table>
                                            <TableBody>
                                                {rooms.map((r) => {
                                                    const createdIso = new Date((r.created_at ?? 0) * 1000).toISOString();
                                                    return (
                                                        <TableRow key={r.room}>
                                                            <TableCell className="font-mono text-sm">{r.room}</TableCell>
                                                            <TableCell>
                                                                <DateFormatter
                                                                    variant="short"
                                                                    className="text-muted-foreground text-sm"
                                                                    dateString={createdIso}
                                                                />
                                                            </TableCell>
                                                            <TableCell className="text-sm">{formatTtl(r.ttl_remaining)}</TableCell>
                                                            <TableCell className="text-right space-x-2">
                                                                {/* Dropdown for copying the link */}
                                                                <DropdownMenu>
                                                                    <DropdownMenuTrigger asChild>
                                                                        <Button
                                                                            size="icon"
                                                                            variant="ghost"
                                                                            aria-label="Copy link"
                                                                            title="Copy link"
                                                                        >
                                                                            <Copy className="h-4 w-4" />
                                                                        </Button>
                                                                    </DropdownMenuTrigger>
                                                                    <DropdownMenuContent align="end" className="w-44">
                                                                        <DropdownMenuItem
                                                                            onClick={() => copyRoomLink(r.room, "normal")}
                                                                            className="flex items-center"
                                                                        >
                                                                            <Copy className="mr-2 w-4 h-4" />
                                                                            Normal Link
                                                                        </DropdownMenuItem>
                                                                        <DropdownMenuItem
                                                                            onClick={() => copyRoomLink(r.room, "public")}
                                                                            className="flex items-center"
                                                                        >
                                                                            <ExternalLink className="mr-2 w-4 h-4" />
                                                                            Public Link
                                                                        </DropdownMenuItem>
                                                                    </DropdownMenuContent>
                                                                </DropdownMenu>
                                                                <Button
                                                                    size="icon"
                                                                    onClick={() =>
                                                                        window.open(route("meet.redirect", { room: r.room }), "_blank")
                                                                    }
                                                                    aria-label="Join"
                                                                    title="Join"
                                                                >
                                                                    <ExternalLink className="h-4 w-4" />
                                                                </Button>
                                                            </TableCell>
                                                        </TableRow>
                                                    );
                                                })}
                                            </TableBody>
                                        </Table>
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground">No active rooms yet</p>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Modal with link to created meeting */}
            <Dialog open={isModalOpen} onOpenChange={setIsModalOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Meeting Link</DialogTitle>
                        <DialogDescription>Copy the link or join right now.</DialogDescription>
                    </DialogHeader>

                    <div className="space-y-8">
                        <div className="flex gap-2">
                            <Input type="text" value={createdLink} readOnly className="w-full" />
                            <Button onClick={handleCopyLink} variant="secondary">
                                Copy
                            </Button>
                        </div>
                        <div className="flex">
                            <Button
                                size="lg"
                                onClick={handleEnterCreated}
                                className="bg-primary hover:bg-primary/90 w-full"
                            >
                                Join
                            </Button>
                        </div>
                    </div>

                    <DialogFooter />
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
