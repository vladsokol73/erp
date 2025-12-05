import { ZoomIn } from "lucide-react";
import VideoPreview from "@/components/common/video-preview";

interface MediaPreviewProps {
    type: string;
    src: string;
    thumbnailUrl?: string | null;
    onPreviewOpen: () => void;
}

// Компонент для отображения медиа (фото или видео) с возможностью увеличения
const MediaPreview = ({ type, src, onPreviewOpen, thumbnailUrl }: MediaPreviewProps) => {
    // Отображаем разные элементы в зависимости от типа медиа
    if (type === "image") {
        return (
            <div className="h-64 w-full rounded-md overflow-hidden group relative">
                <img
                    alt="preview"
                    src={src}
                    className="w-full h-full object-cover object-top transition-transform duration-500 ease-in-out group-hover:scale-105"
                />
                <div
                    onClick={onPreviewOpen}
                    className="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-500 flex items-center justify-center group-hover:opacity-100 cursor-zoom-in"
                >
                    <ZoomIn size={48}/>
                </div>
            </div>
        );
    } else {
        return (
            <div className="h-64 w-full rounded-md overflow-hidden group relative">
                <VideoPreview
                    fillMode="contain"
                    onPlay={onPreviewOpen}
                    url={src}
                    thumbnailUrl={thumbnailUrl}
                />
            </div>
        );
    }
};

export default MediaPreview;
