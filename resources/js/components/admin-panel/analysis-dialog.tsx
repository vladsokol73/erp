"use client";

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";

interface Props {
    open: boolean;
    onClose: () => void;
    title?: string;
    text?: string | null;
}

export default function AnalysisModal({ open, onClose, title, text }: Props) {
    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="!max-w-2xl max-h-[90vh] overflow-hidden">
                <DialogHeader>
                    <DialogTitle>{title ?? "Analysis"}</DialogTitle>
                </DialogHeader>

                <div className="max-h-[70vh] overflow-auto">
                    {text ? (
                        <pre className="whitespace-pre-wrap text-sm text-muted-foreground">
              {text}
            </pre>
                    ) : (
                        <div className="text-sm text-muted-foreground">No analysis.</div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
