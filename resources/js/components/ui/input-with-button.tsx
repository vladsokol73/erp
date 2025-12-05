import React, { useId } from "react"
import { SendIcon } from "lucide-react"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

interface InputWithButtonProps {
    value: string
    onChange: (value: string) => void
    placeholder?: string
    type?: string
    label?: string | null


    onButtonClick: (value: string) => void
    buttonIcon?: React.ReactNode
    buttonAriaLabel?: string
    buttonDisabled?: boolean

    error?: string | null
}

export default function InputWithButton({
                                            value,
                                            onChange,
                                            placeholder = "Email",
                                            type = "email",
                                            label = null,
                                            onButtonClick,
                                            buttonIcon = <SendIcon size={16} />,
                                            buttonAriaLabel = "Subscribe",
                                            buttonDisabled = false,
                                            error = null
                                        }: InputWithButtonProps) {
    const id = useId()
    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        onChange(e.target.value)
    }

    const handleButtonClick = () => {
        onButtonClick(value)
    }

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter' && !buttonDisabled) {
            e.preventDefault()
            handleButtonClick()
        }
    }

    return (
        <div className={label ? "*:not-first:mt-2" : ""}>
            {label && <Label htmlFor={id}>{label}</Label>}
            <div className="relative">
                <Input
                    id={id}
                    className={`pe-9 ${error ? "border-red-500 focus-visible:ring-red-500" : ""}`}
                    placeholder={placeholder}
                    type={type}
                    value={value}
                    onChange={handleInputChange}
                    onKeyDown={handleKeyDown}
                    aria-invalid={error ? "true" : "false"}
                    aria-describedby={error ? `${id}-error` : undefined}
                />
                <button
                    className="text-muted-foreground/80 hover:text-foreground focus-visible:border-ring focus-visible:ring-ring/50 absolute inset-y-0 end-0 flex h-full w-9 items-center justify-center rounded-e-md transition-[color,box-shadow] outline-none focus:z-10 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
                    aria-label={buttonAriaLabel}
                    onClick={handleButtonClick}
                    disabled={buttonDisabled}
                >
                    {buttonIcon}
                </button>
            </div>
            {error && (
                <p
                    id={`${id}-error`}
                    className="mt-2 text-sm text-red-500"
                >
                    {error}
                </p>
            )}
        </div>
    )
}
