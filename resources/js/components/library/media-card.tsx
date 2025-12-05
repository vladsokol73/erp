import { useState } from "react";
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
} from "@/components/ui/card";
import MediaCardHeader from "./media-card-header";
import MediaPreview from "./media-preview";
import MediaCardFooter from "./media-card-footer";
import CommentDialog from "./comments/comment-dialog";
import { TagCreative } from "@/components/library/types"
import DownloadDialog from "./download/download-dialog";
import EditDialog from "@/components/library/edit/edit-dialog";
import { Badge } from "../ui/badge";

import { RectangleHorizontal, RectangleVertical, Square } from "lucide-react";
import { Tooltip, TooltipContent, TooltipTrigger } from "../ui/tooltip";

interface Comment {
    id: string | number;
    author: {
        name: string;
        avatarFallback: string;
    };
    date: string;
    content: string;
}

interface MediaCardProps {
    id: number;
    country: {
        name: string;
        flagUrl: string;
    };
    date: string;
    tags: App.DTO.Creative.TagDto[];
    allTags: App.DTO.Creative.TagDto[];
    mediaType: string;
    mediaSrc: string;
    resolution: string;
    thumbnailUrl: string | null;
    code: string;
    likeCount: number;
    dislikeCount: number;
    userLiked: boolean;
    userDisliked: boolean;
    comments: Comment[];
    favorite: boolean;
    statistic: App.DTO.Creative.CreativeStatisticDto | null;


    onSetFavorite: (id: number, favorite: boolean) => void;

    onPreviewOpen: () => void;

    onAddComment?: (id: number, comment: string) => void;

    onLike?: (id:  number) => void;
    onDislike?: (id: number) => void;

    onOriginalDownload?: (id:  number) => void;
    onUniqueDownload?: (id: number) => void;

    onSave?: (id: number, selectedTagIds: TagCreative[]) => Promise<void>;
    onDelete?: (id: number) => Promise<void>;
}

// Компонент медиа-карточки (фото или видео)
const MediaCard = ({
                       id,
                       country,
                       date,
                       tags,
                       allTags,
                       mediaType,
                       mediaSrc,
                       resolution,
                       thumbnailUrl,
                       code,
                       likeCount,
                       dislikeCount,
                       userLiked,
                       userDisliked,
                       comments,
                       favorite,
                       statistic,

                       onSetFavorite,

                       onPreviewOpen,

                       onAddComment,

                       onLike,
                       onDislike,

                       onOriginalDownload,
                       onUniqueDownload,

                       onSave,
                       onDelete,
                   }: MediaCardProps) => {
    const [isCommentDialogOpen, setIsCommentDialogOpen] = useState(false);

    const [isDownloadDialogOpen, setIsDownloadDialogOpen] = useState(false);

    const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);

    // Обработчики событий с передачей ID медиа
    const handleLike = () => onLike && onLike(id);
    const handleDislike = () => onDislike && onDislike(id);
    const handleOriginalDownload = () => onOriginalDownload && onOriginalDownload(id);
    const handleUniqueDownload = () => onUniqueDownload && onUniqueDownload(id);
    const handleAddComment = (comment: string) => onAddComment && onAddComment(id, comment);
    const handleSetFavorite = (favorite: boolean) => onSetFavorite && onSetFavorite(id, favorite);
    const handleSave = (selectedTagIds: TagCreative[]): Promise<void> => {
        if (onSave) {
            return onSave(id, selectedTagIds);
        }
        return Promise.resolve();
    };
    const handleDelete = (): Promise<void> => {
        if (onDelete) {
            return onDelete(id);
        }
        return Promise.resolve();
    };

    const getAspectRatioIcon = (resolution: string) => {
        const [w, h] = resolution.split(":").map(Number);

        if (w > h) return <RectangleHorizontal className="!size-5" />;
        if (w < h) return <RectangleVertical className="!size-5" />;
        return <Square className="!size-5" />;
    };

    return (
        <>
            <Card>
                <CardHeader>
                    <MediaCardHeader
                        country={country}
                        date={date}
                        tags={tags}
                        favorite={favorite}
                        statistic={statistic}
                        onSetFavorite={handleSetFavorite}
                        onEditClick={() => setIsEditDialogOpen(true)}
                    />
                </CardHeader>
                <div className="flex-1 -my-4"></div>
                <CardContent>
                    <div className="relative">
                        <Badge className="absolute top-2 right-2 z-5 px-1.5 h-8 text-sm flex items-center gap-1" variant="secondary">
                            {getAspectRatioIcon(resolution)}
                            <span className="text-muted-foreground text-xs">{resolution}</span>
                        </Badge>
                        <MediaPreview
                            type={mediaType}
                            src={mediaSrc}
                            onPreviewOpen={onPreviewOpen}
                            thumbnailUrl={thumbnailUrl}
                        />
                    </div>
                </CardContent>
                <CardFooter>
                    <MediaCardFooter
                        code={code}
                        likeCount={likeCount}
                        dislikeCount={dislikeCount}
                        userLiked={userLiked}
                        userDisliked={userDisliked}
                        onLike={handleLike}
                        onDislike={handleDislike}
                        onOriginalDownload={handleOriginalDownload}
                        onUniqueDownload={() => setIsDownloadDialogOpen(true)}
                        commentCount={comments.length}
                        onCommentsClick={() => setIsCommentDialogOpen(true)}
                    />
                </CardFooter>
            </Card>

            <CommentDialog
                isOpen={isCommentDialogOpen}
                onClose={() => setIsCommentDialogOpen(false)}
                comments={comments}
                onAddComment={handleAddComment}
            />

            <DownloadDialog
                isOpen={isDownloadDialogOpen}
                onClose={() => setIsDownloadDialogOpen(false)}
                title="Download Creative"
                creativeUrl={mediaSrc}
            />

            <EditDialog
                currentTags={tags}
                allTags={allTags}
                isOpen={isEditDialogOpen}
                onClose={() => setIsEditDialogOpen(false)}
                onSave={handleSave}
                onDelete={handleDelete}
            />
        </>
    );
};

export default MediaCard;
