import React from "react";
import { X, FileIcon } from "lucide-react";
import { Button } from "@/components/ui/button";
import { FileField } from "./file-field";

export interface SmartFileFieldProps {
    value: File | string | null;
    onChange: (value: File | null) => void;
    error?: string;
    label?: string;
}

export const SmartFileField: React.FC<SmartFileFieldProps> = ({
                                                                  value,
                                                                  onChange,
                                                                  error,
                                                                  label,
                                                              }) => {
    const isRemoteUrl = typeof value === "string" && /^https?:\/\//.test(value);

    // Если есть ссылка на загруженный файл — отображаем её и кнопку сброса
    if (isRemoteUrl) {
        return (
            <div className="flex flex-col gap-1">
                {label && <span className="text-sm text-muted-foreground">{label}</span>}

                <div className="flex items-center justify-between border rounded-md px-3 py-2">
                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                        <FileIcon className="h-4 w-4 text-muted" />
                        <a
                            href={value}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="hover:underline"
                        >
                            Uploaded file
                        </a>
                    </div>

                    <Button
                        variant="outline"
                        size="icon"
                        onClick={() => onChange(null)}
                        className="text-red-500 hover:text-red-600"
                    >
                        <X className="h-4 w-4" />
                    </Button>
                </div>

                {error && <p className="text-sm text-destructive mt-1">{error}</p>}
            </div>
        );
    }

    // Иначе рендерим стандартное поле
    return (
        <FileField
            label={label}
            error={error}
            onChange={onChange}
        />
    );
};
