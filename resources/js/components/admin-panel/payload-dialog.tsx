"use client";

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import DialogMessages from "@/components/admin-panel/dialog-messages";

interface Props {
    open: boolean;
    onClose: () => void;
    title?: string;
    payload: unknown;
}

export default function PayloadModal({ open, onClose, title, payload }: Props) {
    const messages: string[] =
        typeof payload === "object" &&
        payload !== null &&
        "messages" in (payload as Record<string, unknown>)
            ? ((payload as Record<string, unknown>).messages as string[])
            : [];

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent className="!max-w-4xl max-h-[90vh] overflow-hidden">
                <DialogHeader>
                    <DialogTitle className="text-sm font-medium">
                        {title ?? "Диалог"}
                    </DialogTitle>
                </DialogHeader>

                <DialogMessages messages={messages} />
            </DialogContent>
        </Dialog>
    );
}
