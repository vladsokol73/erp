import { useState } from "react";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { Send, SendHorizontal } from "lucide-react";

interface CommentFormProps {
    ticketId: number;
    onSubmit: (ticketId: number, comment: string) => void;
}

export default function CommentForm({ ticketId, onSubmit }: CommentFormProps) {
    const [comment, setComment] = useState("");

    const handleSubmit = () => {
        if (!comment.trim()) return;
        onSubmit(ticketId, comment);
        setComment(""); // очистка после отправки
    };

    return (
        <div className="flex flex-col gap-4">
            <Textarea
                value={comment}
                onChange={(e) => setComment(e.target.value)}
                placeholder="Enter your comment..."
            />
            <div className="flex justify-end items-center">
                <Button onClick={handleSubmit}>
                    Send
                    <SendHorizontal/>
                </Button>
            </div>
        </div>
    );
}
