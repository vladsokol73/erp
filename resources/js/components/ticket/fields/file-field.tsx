import React, { useId, useRef, useState } from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { X } from "lucide-react";
import { cn } from "@/lib/utils";

export interface FileFieldProps {
    label?: string;
    onChange?: (file: File | null) => void;
    error?: string;
}

export const FileField: React.FC<FileFieldProps> = ({
                                                        label,
                                                        onChange,
                                                        error,
                                                    }) => {
    const id = useId();
    const inputRef = useRef<HTMLInputElement>(null);
    const [fileName, setFileName] = useState<string>("");

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] ?? null;

        if (file) {
            setFileName(file.name);
        } else {
            setFileName("");
        }

        if (onChange) {
            onChange(file);
        }
    };

    const handleClear = () => {
        if (inputRef.current) {
            inputRef.current.value = "";
        }
        setFileName("");
        if (onChange) {
            onChange(null);
        }
    };

    return (
        <div className="flex flex-col gap-1">
            {label && <Label htmlFor={id}>{label}</Label>}

            <div className="flex items-center gap-2">
                <Input
                    ref={inputRef}
                    id={id}
                    type="file"
                    onChange={handleChange}
                    className={cn(
                        "p-0 pe-3 file:me-3 file:border-0 file:border-e",
                        error && "border-destructive"
                    )}
                />

                {fileName && (
                    <div className="text-sm text-muted-foreground flex items-center gap-2">
                        <button
                            type="button"
                            onClick={handleClear}
                            className="text-xs text-red-500 hover:text-red-700"
                        >
                            <X className="h-4 w-4" />
                        </button>
                    </div>
                )}
            </div>

            {error && (
                <p className="text-sm text-destructive mt-1">
                    {error}
                </p>
            )}
        </div>
    );
};
