import CommentItem from "./comment-item";

interface Comment {
    id: string | number;
    author: {
        name: string;
        avatarFallback: string;
    };
    date: string;
    content: string;
}

interface CommentListProps {
    comments: Comment[];
}

// Компонент списка комментариев
const CommentList = ({ comments }: CommentListProps) => {
    if (comments.length === 0) {
        return <div className="text-center text-muted-foreground">No comments yet</div>;
    }

    return (
        <div className="flex flex-col gap-6">
            {comments.map((comment) => (
                <CommentItem
                    key={comment.id}
                    author={comment.author}
                    date={comment.date}
                    content={comment.content}
                />
            ))}
        </div>
    );
};

export default CommentList;
