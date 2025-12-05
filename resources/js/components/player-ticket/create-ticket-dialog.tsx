import React from "react";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from "@/components/ui/dialog";
import {
    Form,
    FormField,
    FormItem,
    FormLabel,
    FormControl,
    FormMessage,
} from "@/components/ui/form";
import {
    Select,
    SelectTrigger,
    SelectContent,
    SelectItem,
    SelectValue,
} from "@/components/ui/select";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";
import { LoaderCircle, Paperclip } from "lucide-react";
import { toast } from "sonner";

const fileSchema = z
    .instanceof(File, { message: "Screenshot is required" })
    .refine((f) => ["image/png", "image/jpeg", "image/webp"].includes(f.type), {
        message: "Only PNG / JPEG / WebP allowed",
    })
    .refine((f) => f.size <= 5 * 1024 * 1024, {
        message: "Max file size is 5MB",
    });

// Статическая схема формы
const formSchema = z.object({
    type: z.enum(["fd", "rd"], { required_error: "Type is required" }),
    player_id: z
        .string()
        .min(1, "Player is required")
        .regex(/^\d+$/, "Must be digits only"),
    tg_id: z
        .string()
        .min(1, "Telegram ID is required")
        .regex(/^\d+$/, "Must be digits only"),
    screen: fileSchema,
    sum: z.string().min(1, "Sum is required"),
});

type FormValues = z.infer<typeof formSchema>;

interface CreateTicketDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onCreated: (ticket: App.DTO.Ticket.PlayerTicketListDto) => void;
}

export default function CreateTicketDialog({
                                               open,
                                               onOpenChange,
                                               onCreated,
                                           }: CreateTicketDialogProps) {
    const api = useApi();

    const form = useForm<FormValues>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            type: "fd",
            player_id: "",
            tg_id: "",
            sum: "",
        } as Partial<FormValues>,
    });


    const onSubmit = async (data: FormValues) => {

        const fd = new FormData();
        fd.append("type", data.type);
        fd.append("player_id", data.player_id);
        fd.append("tg_id", data.tg_id);
        fd.append("sum", data.sum);
        fd.append("screen", data.screen);

        await api.post(route("tickets.player.create"), fd, {
            requestName: "createTicket",
            onSuccess: (res) => {
                const ticket =
                    (res?.ticket as App.DTO.Ticket.PlayerTicketListDto | undefined);
                if (ticket) {
                    toast.success("Ticket created successfully");
                    onCreated(ticket);
                    form.reset({
                        type: "fd",
                        player_id: "",
                        tg_id: "",
                        sum: "",
                    });
                    onOpenChange(false);
                } else {
                    toast.success("Ticket created");
                    onOpenChange(false);
                }
            },
            onError: (error) => {
                const message = error || "Unknown error";
                form.setError("root", { message });
                toast.error("Error creating ticket.");
            },
        });
    };

    const isSubmitting = api.isLoading.request("createTicket");

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-w-lg">
                <DialogHeader>
                    <DialogTitle>Create Ticket</DialogTitle>
                    <DialogDescription>Fill in the details below</DialogDescription>
                </DialogHeader>

                <Form {...form}>
                    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
                        {/* Type */}
                        <FormField
                            control={form.control}
                            name="type"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Type</FormLabel>
                                    <FormControl>
                                        <Select onValueChange={field.onChange} value={field.value}>
                                            <SelectTrigger className="w-full">
                                                <SelectValue placeholder="Select Type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="fd">FD</SelectItem>
                                                <SelectItem value="rd">RD</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {/* Player ID */}
                        <FormField
                            control={form.control}
                            name="player_id"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Player ID</FormLabel>
                                    <FormControl>
                                        <Input
                                            inputMode="numeric"
                                            placeholder="Enter Player ID"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {/* Telegram ID */}
                        <FormField
                            control={form.control}
                            name="tg_id"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Telegram ID</FormLabel>
                                    <FormControl>
                                        <Input
                                            inputMode="numeric"
                                            placeholder="123456789"
                                            {...field}
                                        />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {/* Screenshot (file) */}
                        <FormField
                            control={form.control}
                            name="screen"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Screenshot</FormLabel>
                                    <FormControl>
                                        {/* Важно: для файлов используем onChange с files[0] */}
                                        <div className="flex items-center gap-3">
                                            <Input
                                                type="file"
                                                accept="image/png, image/jpeg, image/webp"
                                                onChange={(e) => {
                                                    const file = e.target.files?.[0];
                                                    field.onChange(file);
                                                }}
                                            />
                                            <Paperclip className="size-4 shrink-0 opacity-60" />
                                        </div>
                                    </FormControl>
                                    {/* Покажем выбранный файл (если есть) */}
                                    {field.value && (
                                        <p className="text-xs text-muted-foreground mt-1">
                                            {(field.value as File).name} —{" "}
                                            {Math.round((field.value as File).size / 1024)} KB
                                        </p>
                                    )}
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {/* Sum */}
                        <FormField
                            control={form.control}
                            name="sum"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Sum</FormLabel>
                                    <FormControl>
                                        <Input placeholder="100.00" {...field} />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            )}
                        />

                        {/* Ошибка корня формы (например, с сервера) */}
                        {form.formState.errors.root?.message && (
                            <p className="text-sm text-destructive">
                                {form.formState.errors.root.message}
                            </p>
                        )}

                        <div className="flex justify-end">
                            <Button
                                className={cn(isSubmitting && "pointer-events-none opacity-50")}
                                type="submit"
                            >
                                {isSubmitting && <LoaderCircle className="mr-2 animate-spin" />}
                                Submit
                            </Button>
                        </div>
                    </form>
                </Form>
            </DialogContent>
        </Dialog>
    );
}
