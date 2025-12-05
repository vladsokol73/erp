import { Badge } from "@/components/ui/badge";
import {BarChart, Dot, Pencil, Pointer, Users} from "lucide-react";
import { TagCreative } from "@/components/library/types"
import {DateFormatter} from "@/components/common/date-formatter"
import BookmarkIcon from "@/components/library/bookmark-icon";
import {Button} from "@/components/ui/button";
import Permission from "@/components/common/permission";

interface MediaCardHeaderProps {
    country: {
        name: string;
        flagUrl: string;
    };
    date: string;
    tags: TagCreative[];
    favorite: boolean;
    statistic: App.DTO.Creative.CreativeStatisticDto | null;

    onSetFavorite: (favorite: boolean) => void
    onEditClick?: () => void;
}

const MediaCardHeader = ({
                             country,
                             date,
                             tags,
                             favorite,
                             statistic,

                             onSetFavorite,
                             onEditClick
                         }: MediaCardHeaderProps) => {
    return (
        <div className="flex justify-between items-center">
            <div className="flex flex-col gap-2">
                <div className="flex gap-4">
                    <img
                        alt={country.name}
                        className="size-10 rounded-full"
                        src={country.flagUrl}
                    />
                    <div className="flex flex-col">
                        <h3 className="font-semibold">{country.name}</h3>
                        <div className="flex items-center">
                            <DateFormatter
                                variant="short"
                                className="text-muted-foreground text-sm"
                                dateString={date}
                            />
                            <Dot/>
                            <DateFormatter
                                variant="relative"
                                className="text-foreground text-sm"
                                autoRefresh
                                dateString={date}
                            />
                        </div>
                    </div>
                </div>
                <div className="flex items-center py-2 gap-4">
                    <div className="flex items-center gap-2">
                        <Pointer className="size-4" />
                        <span className="text-sm text-muted-foreground">{statistic?.clicks}</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <BarChart className="size-4" />
                        <span className="text-sm text-muted-foreground">{statistic?.ctr} %</span>
                    </div>
                    <div className="flex items-center gap-2">
                        <Users className="size-4" />
                        <span className="text-sm text-muted-foreground">{statistic?.leads}</span>
                    </div>
                </div>
                <div className="flex items-center flex-wrap gap-2">
                    {tags.map((tag, index) => (
                        <Badge
                            key={tag.id}
                            className={`bg-${tag.style}`}
                        >
                            {tag.name}
                        </Badge>
                    ))}

                    <Permission allow="creatives.update">
                        <Pencil
                            onClick={onEditClick}
                            className="ml-2 cursor-pointer hover:text-muted-foreground"
                            size={16}
                        />
                    </Permission>

                </div>
            </div>

            <BookmarkIcon
                aria-label="Toggle bookmark"
                selected={favorite}
                onSelectedChange={(selected) => onSetFavorite(selected)}
            />
        </div>
    );
};

export default MediaCardHeader;
