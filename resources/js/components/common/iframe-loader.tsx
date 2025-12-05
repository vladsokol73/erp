// components/ui/iframe-with-loader.tsx
import { useState } from "react";
import { Loader2 } from "lucide-react";

type IframeWithLoaderProps = {
    src: string;
    className?: string;
};

export default function IframeLoader({ src, className }: IframeWithLoaderProps) {
    const [isLoading, setIsLoading] = useState(true);

    return (
        <div className={`relative w-full h-full rounded-sm overflow-hidden ${className ?? ""}`}>
            {isLoading && (
                <div className="absolute inset-0 z-10 flex items-center justify-center backdrop-blur-sm">
                    <Loader2 className="w-6 h-6 animate-spin text-muted-foreground" />
                </div>
            )}

            <iframe
                src={src}
                className="w-full h-full"
                onLoad={() => setIsLoading(false)}
                allowFullScreen
            />
        </div>
    );
}
