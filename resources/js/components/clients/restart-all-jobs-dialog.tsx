import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";
import { ListRestart } from "lucide-react";
import React from "react";

interface Props {
    onConfirm: () => Promise<void> | void;
}

export default function RestartAllJobsDialog({ onConfirm }: Props) {
    const [open, setOpen] = React.useState(false);

    const handleConfirm = async () => {
        await onConfirm();
        setOpen(false);
    };

    return (
        <AlertDialog open={open} onOpenChange={setOpen}>
            <AlertDialogTrigger asChild>
                <Button className="w-fit" variant="default">
                    <ListRestart />
                    Restart All Jobs
                </Button>
            </AlertDialogTrigger>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Restart all failed jobs?</AlertDialogTitle>
                </AlertDialogHeader>
                <p>This will restart all failed queue jobs.</p>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction onClick={handleConfirm}>
                        Confirm
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
