"use client"

import { LucideIcon } from "lucide-react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"

interface BadgeButtonProps {
    icon: LucideIcon
    count?: number
    maxCount?: number
    onClick?: () => void
    className?: string
    badgeClassName?: string
    variant?: "default" | "destructive" | "outline" | "secondary" | "ghost" | "link"
    size?: "default" | "sm" | "lg" | "icon"
    ariaLabel?: string
}

export function BadgeButton({
                                icon: Icon,
                                count = 0,
                                maxCount = 99,
                                onClick,
                                className = "",
                                badgeClassName = "",
                                variant = "outline",
                                size = "icon",
                                ariaLabel = "Button with badge",
                            }: BadgeButtonProps) {
    // Вычисляем отображаемое значение счетчика
    const displayCount = count > maxCount ? `${maxCount}+` : count

    return (
        <Button
            variant={variant}
            size={size}
            className={`relative ${className}`}
            onClick={onClick}
            aria-label={ariaLabel}
        >
            <Icon size={16} aria-hidden="true" />
            {count > 0 && (
                <Badge
                    className={`absolute -top-2 left-full min-w-5 -translate-x-1/2 px-1 ${badgeClassName}`}
                    variant="default"
                >
                    {displayCount}
                </Badge>
            )}
        </Button>
    )
}
