import React, { useEffect, useRef } from "react";
import { MessageSquareOff } from "lucide-react";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { CommentItem } from "@/components/library";
import CommentForm from "@/components/ticket/my/comment-form";
import { CommentCreative } from "@/components/library/types";

interface TicketCommentsProps {
    comments: CommentCreative[];
    ticketId: number;
    handleAddComment: (ticketId: number, comment: string) => void;
}

const TicketComments = ({
                            comments,
                            ticketId,
                            handleAddComment
                        }: TicketCommentsProps) => {
    const commentsRef = useRef<HTMLDivElement>(null);

    // Прокрутка вниз при изменении списка комментариев
    useEffect(() => {
        if (commentsRef.current) {
            commentsRef.current.scrollTop = commentsRef.current.scrollHeight;
        }
    }, [comments]);

    return (
        <Card className="mt-4">
            <CardHeader className="border-b">
                <CardTitle className="flex items-center gap-2">
                    Comments
                    {comments.length > 0 && (
                        <Badge variant="secondary">{comments.length}</Badge>
                    )}
                </CardTitle>
                <CardDescription>Comments about this ticket</CardDescription>
            </CardHeader>
            <CardContent>
                <div
                    ref={commentsRef}
                    className="flex flex-col max-h-[350px] overflow-y-auto gap-4 mb-6"
                >
                    {comments.length === 0 ? (
                        <div className="flex flex-col gap-2 items-center text-sm text-center text-muted-foreground">
                            <MessageSquareOff className="size-8" />
                            No comments
                        </div>
                    ) : (
                        comments.map((comment) => (
                            <CommentItem
                                key={comment.id}
                                commentId={comment.id}
                                author={{
                                    name: comment.user_name,
                                    avatarFallback: comment.user_name
                                        .slice(0, 2)
                                        .toUpperCase(),
                                }}
                                date={comment.created_at}
                                content={comment.comment}
                            />
                        ))
                    )}
                </div>
                <CommentForm
                    ticketId={ticketId}
                    onSubmit={handleAddComment}
                />
            </CardContent>
        </Card>
    );
};

export default TicketComments;
