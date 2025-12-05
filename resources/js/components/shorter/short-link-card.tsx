import React, { useEffect, useState } from "react";
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardDescription,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Check, Copy, Link, Loader2 } from "lucide-react";
import { cn } from "@/lib/utils";

interface Props {
    originalUrl: string;
    shortUrl: string | null;
    loading: boolean;
    error?: string | null;
}

export const ShortLinkCard = ({ originalUrl, shortUrl, loading, error }: Props) => {
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        if (!shortUrl) return;
        await navigator.clipboard.writeText(shortUrl);
        setCopied(true);
        setTimeout(() => setCopied(false), 1000);
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle>Short URL Process</CardTitle>
                <CardDescription>Step-by-step result</CardDescription>
            </CardHeader>

            <CardContent className="space-y-6">

                {/* Step 1 — Original */}
                <div className="relative flex items-start gap-4">
                    <div className="flex flex-col items-center">
                        <div className="flex size-9 items-center justify-center rounded-full border text-sm text-muted-foreground">
                            {loading ? (
                                <Loader2 className="w-4 h-4 animate-spin" />
                            ) : (
                                <Check className="w-4 h-4 text-green-500" />
                            )}
                        </div>
                        {(shortUrl || error) && <div className="h-full w-px bg-border" />}
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground mb-2">Original URL</p>
                        <span className="text-sm max-w-48 truncate text-foreground">
                            {originalUrl}
                        </span>
                    </div>
                </div>

                {/* Step 2 — Error OR Short URL */}
                {error ? (
                    <div className="relative flex items-start gap-4">
                        <div className="flex flex-col items-center">
                            <div className="flex size-9 items-center justify-center rounded-full border border-destructive text-destructive">
                                !
                            </div>
                        </div>

                        <div>
                            <p className="text-sm text-muted-foreground">Error</p>
                            <p className="text-sm text-destructive max-w-72">
                                {error}
                            </p>
                        </div>
                    </div>
                ) : (
                    shortUrl && (
                        <div className="relative flex items-start gap-4">
                            <div className="flex flex-col items-center">
                                <div className="flex size-9 items-center justify-center rounded-full border text-sm text-muted-foreground">
                                    <Link className="w-4 h-4" />
                                </div>
                            </div>

                            <div>
                                <p className="text-sm text-muted-foreground">Short URL</p>
                                <div className="flex gap-2 items-center justify-between">
                                    <span className="text-sm max-w-48 truncate text-foreground">
                                        {shortUrl}
                                    </span>
                                    <Button
                                        onClick={handleCopy}
                                        size="icon"
                                        variant="ghost"
                                        className="hover:bg-transparent"
                                    >
                                        {copied ? (
                                            <Check className="w-4 h-4" />
                                        ) : (
                                            <Copy className="w-4 h-4" />
                                        )}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    )
                )}

            </CardContent>
        </Card>
    );
};

