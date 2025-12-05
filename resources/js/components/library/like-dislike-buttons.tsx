import React from 'react';
import { ThumbsUp, ThumbsDown } from "lucide-react";

interface LikeDislikeButtonsProps {
    likeCount: number;
    dislikeCount: number;
    userLiked: boolean;
    userDisliked: boolean;
    onLike?: () => void;
    onDislike?: () => void;
}


// Компонент с кнопками лайк/дизлайк и счетчиками
const LikeDislikeButtons = ({
                                likeCount,
                                dislikeCount,
                                userLiked,
                                userDisliked,
                                onLike,
                                onDislike
                            }: LikeDislikeButtonsProps) => {
    return (
        <div className="flex gap-4 items-center">
            {/* Лайк */}
            <div
                className="flex items-center cursor-pointer"
                onClick={onLike}
            >
                <ThumbsUp
                    className={userLiked ? "text-green" : "text-muted-foreground"}
                    size={18}
                    strokeWidth={2}
                />
                <span className={`text-sm ml-1.5 ${!userLiked ? "text-muted-foreground" : "text-green"}`}>{likeCount}</span>
            </div>

            {/* Дизлайк */}
            <div
                className="flex items-center cursor-pointer"
                onClick={onDislike}
            >
                <ThumbsDown
                    className={userDisliked ? "text-red" : "text-muted-foreground"}
                    size={18}
                    strokeWidth={2}
                />
                <span className={`text-sm ml-1.5 ${!userDisliked ? "text-muted-foreground" : "text-red"}`}>{dislikeCount}</span>
            </div>
        </div>
    );
};

export default LikeDislikeButtons;
