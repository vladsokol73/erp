"use client";

interface DialogMessagesProps {
    messages: string[];
}

const parseMessage = (raw: string) => {
    const [rolePart, ...rest] = raw.split(":");
    const role = rolePart?.trim() || "Сообщение";
    const text = rest.join(":").trim();
    return { role, text };
};

export default function DialogMessages({ messages }: DialogMessagesProps) {
    if (!messages || messages.length === 0) {
        return (
            <div className="text-xs text-muted-foreground">Нет сообщений</div>
        );
    }

    return (
        <div className="max-h-[70vh] rounded-lg bg-primary/2 overflow-auto space-y-2 p-2">
            {messages.map((raw, idx) => {
                const { role, text } = parseMessage(raw);
                const isClient = role.toLowerCase().includes("клиент");

                return (
                    <div
                        key={idx}
                        className={`flex ${
                            isClient ? "justify-start" : "justify-end"
                        }`}
                    >
                        <div
                            className={`max-w-[65%] rounded-lg px-2 py-1.5 shadow-sm whitespace-pre-wrap text-xs leading-snug ${
                                isClient
                                    ? "bg-muted text-foreground"
                                    : "bg-primary text-primary-foreground"
                            }`}
                        >
                            <div className="mb-0.5 text-[10px] font-semibold uppercase opacity-60">
                                {role}
                            </div>
                            {text || "—"}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
