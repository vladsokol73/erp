"use client"

import { useEffect, useMemo, useState } from "react"
import { BellIcon } from "lucide-react"

import useApi from '@/hooks/use-api';
import { route } from 'ziggy-js';

import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover"
import DateFormatter from "@/components/common/date-formatter";
import {ScrollArea} from "@/components/ui/scroll-area";

type Notification = {
    id: string
    message: string
    created_at: string
    unread: boolean
}

type NotificationsPopoverProps = {
    onNotificationClick?: (id: string) => void
    onMarkAllAsRead?: () => void
}

function Dot({ className }: { className?: string }) {
    return (
        <svg
            width="6"
            height="6"
            fill="currentColor"
            viewBox="0 0 6 6"
            xmlns="http://www.w3.org/2000/svg"
            className={className}
            aria-hidden="true"
        >
            <circle cx="3" cy="3" r="3" />
        </svg>
    )
}

export function NotificationsPopover({
                                         onNotificationClick,
                                         onMarkAllAsRead,
                                     }: NotificationsPopoverProps) {
    const api = useApi();

    const [notifications, setNotifications] = useState<Notification[]>([])

    const fetchNotifications = () => {
        api.get(
            route('notifications.show'),
            {
                requestName: "loadNotifications",
                onSuccess: (data: any) => {
                    setNotifications(data.notifications);
                },
            }
        )
    }

    useEffect(() => {
        fetchNotifications()
    }, [])

    const unreadCount = useMemo(
        () => notifications.filter((n) => n.unread).length,
        [notifications]
    )

    const handleNotificationClick = (id: string) => {
        setNotifications((prev) =>
            prev.map((n) => (n.id === id ? { ...n, unread: false } : n))
        )
        onNotificationClick?.(id)

        api.post(
            route('notifications.read-one'),
            {
                id
            },
            {
                requestName: "readNotification",
                onSuccess: (data: any) => {
                    setNotifications(data.notifications);
                },
            }
        )
    }

    const handleMarkAllAsRead = () => {
        setNotifications((prev) => prev.map((n) => ({ ...n, unread: false })))
        onMarkAllAsRead?.()

        api.post(
            route('notifications.read-all'),
            {

            },
            {
                requestName: "readNotification",
                onSuccess: (data: any) => {
                    setNotifications(data.notifications);
                },
            }
        )
    }

    return (
        <Popover>
            <PopoverTrigger asChild>
                <Button
                    size="icon"
                    variant="outline"
                    className="relative"
                    aria-label="Open notifications"
                >
                    <BellIcon size={16} aria-hidden="true" />
                    {unreadCount > 0 && (
                        <Badge className="absolute -top-2 left-full min-w-5 -translate-x-1/2 px-1">
                            {unreadCount > 99 ? "99+" : unreadCount}
                        </Badge>
                    )}
                </Button>
            </PopoverTrigger>
            <PopoverContent className="w-80 p-1">
                <div className="flex items-baseline justify-between gap-4 px-3 py-2">
                    <div className="text-sm font-semibold">Notifications</div>
                    {unreadCount > 0 && (
                        <button
                            className="text-xs font-medium hover:underline"
                            onClick={handleMarkAllAsRead}
                        >
                            Mark all as read
                        </button>
                    )}
                </div>
                <div
                    role="separator"
                    aria-orientation="horizontal"
                    className="bg-border -mx-1 my-1 h-px"
                />
                <ScrollArea className="h-96 pr-3">
                    {api.isLoading.request("loadNotifications") ? (
                        <div className="text-sm px-3 py-2 text-muted-foreground">Loading...</div>
                    ) : (
                        notifications.map((notification) => (
                            <div
                                key={notification.id}
                                className="hover:bg-accent rounded-md px-3 py-2 text-sm transition-colors"
                            >
                                <div className="relative flex items-start pe-3">
                                    <div className="flex-1 space-y-1">
                                        <button
                                            className="text-foreground/80 text-left after:absolute after:inset-0"
                                            onClick={() => handleNotificationClick(notification.id)}
                                        >
                                    <span className="text-foreground">
                                      {notification.message}
                                    </span>
                                        </button>
                                        <div className="text-muted-foreground text-xs">
                                            <DateFormatter variant="relative" dateString={notification.created_at}/>
                                        </div>
                                    </div>
                                    {notification.unread && (
                                        <div className="absolute end-0 self-center">
                                            <span className="sr-only">Unread</span>
                                            <Dot />
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </ScrollArea>
            </PopoverContent>
        </Popover>
    )
}
