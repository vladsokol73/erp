import { Badge } from "@/components/ui/badge";
import {
    Hash,
    Package,
    List,
    HelpCircle,
    CircleSlash2,
    Text,
    Check,
    X,
    ChevronDown,
    ChevronUp,
} from "lucide-react";
import React from "react";

interface JsonPreviewProps {
    data: object | string | null;
}

export default function JsonPreview({ data }: JsonPreviewProps) {
    const parsed = typeof data === "string" ? tryParseJson(data) : data;
    const [expanded, setExpanded] = React.useState(false);

    if (parsed === null || typeof parsed !== "object") {
        return <ValueBadge value={parsed} />;
    }

    const totalItems = Array.isArray(parsed)
        ? parsed.length
        : Object.keys(parsed).length;

    const shouldCollapse = totalItems > 3;

    return (
        <div className="font-mono text-xs space-y-1">
            <JsonEntries node={parsed} level={0} limit={!expanded && shouldCollapse ? 3 : undefined} />

            {shouldCollapse && (
                <button
                    onClick={() => setExpanded((prev) => !prev)}
                    className="text-xs text-muted-foreground hover:underline inline-flex items-center gap-1 mt-1"
                >
                    {expanded ? (
                        <>
                            Collapse <ChevronUp size={12} />
                        </>
                    ) : (
                        <>
                            Expand <ChevronDown size={12} />
                        </>
                    )}
                </button>
            )}
        </div>
    );
}

function tryParseJson(input: string): object | null {
    try {
        return JSON.parse(input);
    } catch {
        return null;
    }
}

function JsonEntries({
                         node,
                         level,
                         limit,
                     }: {
    node: any;
    level: number;
    limit?: number;
}) {
    if (typeof node !== "object" || node === null) return null;

    const entries = Array.isArray(node)
        ? node.map((v, i) => [i, v])
        : Object.entries(node);

    const shownEntries = typeof limit === "number" ? entries.slice(0, limit) : entries;

    const bgClass =
        level === 1
            ? "bg-muted/10"
            : level === 2
                ? "bg-muted/5"
                : level >= 3
                    ? "bg-muted/3"
                    : "";

    return (
        <div
            className={`space-y-1 rounded-md pl-2 py-2 ${bgClass}`}
            style={{
                borderLeft: "2px solid rgba(100,100,100,0.1)",
                marginLeft: level > 0 ? 8 : 0,
            }}
        >
            {shownEntries.map(([key, value]) => (
                <div
                    key={String(key)}
                    className="flex gap-2 items-center px-1"
                >
                    <div className="text-muted-foreground min-w-[150px] pr-2 text-sm">
                        {key}:
                    </div>
                    <div className="flex-1">
                        {typeof value === "object" && value !== null ? (
                            <div className="flex items-center gap-1">
                                {Array.isArray(value) ? <List size={12} /> : <Package size={12} />}
                                <JsonEntries node={value} level={level + 1} />
                            </div>
                        ) : (
                            <ValueBadge value={value} />
                        )}
                    </div>
                </div>
            ))}
        </div>
    );
}

function ValueBadge({ value }: { value: unknown }) {
    if (value === null || value === undefined) {
        return (
            <Badge
                variant="secondary"
                className="!py-0.5 !px-1 my-1 inline-flex items-center gap-1"
            >
                <CircleSlash2 size={12} /> null
            </Badge>
        );
    }

    switch (typeof value) {
        case "string":
            return (
                <Badge className="bg-orange/15 text-orange !py-0.5 !px-1 my-1 inline-flex items-center gap-1">
                    <Text size={12} />
                    <p className="w-96 whitespace-pre-wrap break-words">{value}</p>
                </Badge>
            );
        case "number":
            return (
                <Badge className="bg-blue/15 text-blue !py-0.5 !px-1 my-1 inline-flex items-center gap-1">
                    <Hash size={12} />
                    {value}
                </Badge>
            );
        case "boolean":
            return (
                <Badge
                    className={`!py-0.5 !px-1 my-1 inline-flex items-center gap-1 ${
                        value
                            ? "bg-green/15 text-green"
                            : "bg-red/15 text-red"
                    }`}
                >
                    {value ? <Check size={12} /> : <X size={12} />}
                    {String(value)}
                </Badge>
            );
        default:
            return (
                <Badge variant="outline" className="italic text-gray-600 !py-0.5 !px-1 my-1 inline-flex items-center gap-1">
                    <HelpCircle size={12} /> unknown
                </Badge>
            );
    }
}
