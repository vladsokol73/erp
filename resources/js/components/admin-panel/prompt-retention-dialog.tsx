"use client";

import { useState } from "react";
import { Bot } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner"; // уведомления

interface Props {
    prompt: string;
    onChange: (value: string) => void;
    onSave: () => void;
    onTest: () => Promise<void>; // теперь просто void
    isSaving?: boolean;
    isTesting?: boolean;
}

export default function PromptRetentionDialog({
                                                  prompt,
                                                  onChange,
                                                  onSave,
                                                  onTest,
                                                  isSaving = false,
                                                  isTesting = false,
                                              }: Props) {
    const [open, setOpen] = useState(false);

    const handleTestClick = async () => {
        await onTest();
        setOpen(false);
    };

    return (
        <div className="flex flex-col gap-6">
            <Button
                className="w-fit"
                onClick={() => {
                    setOpen(true);
                }}
            >
                <Bot className="mr-2 h-4 w-4" />
                Prompt Retention
            </Button>

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="!max-w-4xl max-h-[80vh] overflow-hidden">
                    <DialogHeader>
                        <DialogTitle>Edit LLM Prompt</DialogTitle>
                    </DialogHeader>

                    <Textarea
                        value={prompt}
                        onChange={(e) => onChange(e.target.value)}
                        placeholder="Enter your LLM prompt..."
                        rows={8}
                        className="max-h-[30vh]"
                    />

                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={handleTestClick}
                            disabled={isTesting}
                        >
                            {isTesting ? "Testing..." : "Test"}
                        </Button>
                        <Button
                            variant="outline"
                            onClick={() => setOpen(false)}
                            disabled={isSaving || isTesting}
                        >
                            Cancel
                        </Button>
                        <Button
                            onClick={onSave}
                            disabled={!prompt.trim() || isSaving}
                        >
                            {isSaving ? "Saving..." : "Save"}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
