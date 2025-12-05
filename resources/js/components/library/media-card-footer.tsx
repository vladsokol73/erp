import { Button } from "@/components/ui/button";
import CopyButton from "@/components/ui/copy-button";
import {Download, FileIcon, MessageSquare, MessageSquareMore, StarIcon} from "lucide-react";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import LikeDislikeButtons from "./like-dislike-buttons";
import React from "react";

interface MediaCardFooterProps {
    code: string;
    likeCount: number;
    dislikeCount: number;
    userLiked: boolean;
    userDisliked: boolean;
    onOriginalDownload?: () => void;
    onUniqueDownload?: () => void;
    onLike?: () => void;
    onDislike?: () => void;
    commentCount: number;
    onCommentsClick?: () => void;
}

const MediaCardFooter = ({
                             code,
                             likeCount,
                             dislikeCount,
                             userLiked,
                             userDisliked,
                             onOriginalDownload,
                             onUniqueDownload,
                             onLike,
                             onDislike,
                             commentCount,
                             onCommentsClick
                         }: MediaCardFooterProps) => {
    return (
        <div className="flex flex-col gap-4 w-full">
            <div className="flex items-center gap-4">
                <LikeDislikeButtons
                    likeCount={likeCount}
                    dislikeCount={dislikeCount}
                    userLiked={userLiked}
                    userDisliked={userDisliked}
                    onLike={onLike}
                    onDislike={onDislike}
                />

                <div className="flex items-center cursor-pointer" onClick={onCommentsClick}>
                    {
                        commentCount
                            ? <MessageSquareMore
                                className="text-foreground"
                                size={18}
                                strokeWidth={2}
                            />
                            : <MessageSquare
                                className="text-muted-foreground"
                                size={18}
                                strokeWidth={2}
                            />
                    }
                    <span className={`text-sm ml-1.5 ${!commentCount ? "text-muted-foreground" : "text-foreground"}`}>{commentCount}</span>
                </div>
            </div>

            <div className="flex w-full justify-between items-center">
                <CopyButton
                    textToCopy={code}
                    label="Code"
                    tooltipText="Copy code"
                    showLabel={false}
                    className="w-28"
                />

                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button>
                            <Download aria-hidden="true" /> Download
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DropdownMenuItem onClick={onOriginalDownload}>
                            <FileIcon size={16} className="opacity-60" aria-hidden="true" />
                            Original
                        </DropdownMenuItem>
                        <DropdownMenuItem onClick={onUniqueDownload}>
                            <StarIcon size={16} className="opacity-60" aria-hidden="true" />
                            Unique
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    );
};

export default MediaCardFooter;
