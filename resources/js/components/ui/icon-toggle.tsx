
import React, { useState } from "react"
import { Toggle } from "@/components/ui/toggle"
import { ToggleProps } from "@radix-ui/react-toggle"

interface IconToggleProps extends Omit<ToggleProps, 'children'> {
    onIcon: React.ReactNode
    offIcon: React.ReactNode
    onToggleChange?: (isPressed: boolean) => void
    iconSize?: number
    iconClassName?: string
    variant?: "default" | "outline" | null | undefined
}

export function IconToggle({
                               onIcon,
                               offIcon,
                               onToggleChange,
                               iconSize = 16,
                               iconClassName = "",
                               variant = "default",
                               pressed: controlledPressed,
                               ...props
                           }: IconToggleProps) {

    const [internalPressed, setInternalPressed] = useState(false)

    const pressed = controlledPressed !== undefined
        ? controlledPressed
        : internalPressed
    const handlePressedChange = (newPressed: boolean) => {
        if (controlledPressed === undefined) {
            setInternalPressed(newPressed)
        }

        onToggleChange?.(newPressed)
    }

    return (
        <Toggle
            variant={variant}
            pressed={pressed}
            onPressedChange={handlePressedChange}
            className="group data-[state=on]:hover:bg-muted size-9 data-[state=on]:bg-transparent relative"
            {...props}
        >
            <span
                className={`
                    absolute
                    inset-0
                    flex
                    items-center
                    justify-center
                    shrink-0
                    transition-all
                    ${pressed ? 'scale-100 opacity-100' : 'scale-0 opacity-0'}
                    ${iconClassName}
                `}
                aria-hidden="true"
            >
                {React.cloneElement(onIcon as React.ReactElement, {
                    size: iconSize
                })}
            </span>
            <span
                className={`
                    absolute
                    inset-0
                    flex
                    items-center
                    justify-center
                    shrink-0
                    transition-all
                    ${pressed ? 'scale-0 opacity-0' : 'scale-100 opacity-100'}
                    ${iconClassName}
                `}
                aria-hidden="true"
            >
                {React.cloneElement(offIcon as React.ReactElement, {
                    size: iconSize
                })}
            </span>
        </Toggle>
    )
}
