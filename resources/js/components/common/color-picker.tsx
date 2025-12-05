import { Badge } from "@/components/ui/badge"
import clsx from "clsx"

const colors = [
    "red", "crimson", "rose", "pink", "magenta", "fuchsia",
    "purple", "violet", "indigo", "blue", "azure", "sky",
    "cyan", "teal", "mint", "emerald", "green", "lime",
    "chartreuse", "yellow", "amber", "orange", "tangerine",
    "salmon", "lightpink", "lavender", "lightblue", "lightcyan",
    "lightgreen", "lightyellow", "peach", "coral",
]

export function ColorPicker({ value, onChange, error }: {
    value?: string
    onChange: (val: string) => void
    error?: string
}) {
    return (
        <div className="flex flex-col gap-2">
            <div className="flex flex-wrap gap-2">
                {colors.map((color) => (
                    <Badge
                        key={color}
                        onClick={() => onChange(color)}
                        className={clsx(
                            "cursor-pointer select-none capitalize px-2 py-1",
                            "justify-center",
                            value !== color ? "bg-transparent border-1 border-"+color+" text-"+color : "bg-" + color,
                        )}
                    >
                        {color}
                    </Badge>
                ))}
            </div>
            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    )
}
