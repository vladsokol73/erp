import {useState, useEffect, useRef, useMemo} from "react";
import {
    Card,
    CardContent,
    CardHeader,
} from "@/components/ui/card";
import { ChevronDown, ChevronUp, Funnel } from "lucide-react";
import { IconToggle } from "@/components/ui/icon-toggle";
import MultiSelect from "@/components/ui/multi-select";

// Интерфейс для значений фильтров
interface FilterValues {
    countries: string[];
    users: string[];
    tags: string[];
    types: string[];
}

interface FilterPanelProps {
    countries: { value: string; label: string }[];
    users: { value: string; label: string }[];
    tags: { value: string; label: string }[];
    initialOpen?: boolean;
    onFilterChange?: (filters: FilterValues) => void;
    initialFilters?: Partial<FilterValues>;
}

const FilterPanel = ({
                         countries,
                         users,
                         tags,
                         initialOpen = false,
                         onFilterChange,
                         initialFilters = {}
                     }: FilterPanelProps) => {
    const [filterOpen, setFilterOpen] = useState(initialOpen);

    const prevFiltersRef = useRef<FilterValues | null>(null);

    const [selectedCountries, setSelectedCountries] = useState<string[]>(initialFilters.countries || []);
    const [selectedUsers, setSelectedUsers] = useState<string[]>(initialFilters.users || []);
    const [selectedTags, setSelectedTags] = useState<string[]>(initialFilters.tags || []);
    const [selectedTypes, setSelectedTypes] = useState<string[]>(initialFilters.types || []);

    useEffect(() => {
        const currentFilters: FilterValues = {
            countries: selectedCountries,
            users: selectedUsers,
            tags: selectedTags,
            types: selectedTypes
        };

        if (prevFiltersRef.current === null) {
            prevFiltersRef.current = currentFilters;
            return;
        }

        const hasChanged =
            JSON.stringify(prevFiltersRef.current.countries) !== JSON.stringify(currentFilters.countries) ||
            JSON.stringify(prevFiltersRef.current.users) !== JSON.stringify(currentFilters.users) ||
            JSON.stringify(prevFiltersRef.current.tags) !== JSON.stringify(currentFilters.tags) ||
            JSON.stringify(prevFiltersRef.current.types) !== JSON.stringify(currentFilters.types);

        prevFiltersRef.current = currentFilters;

        if (hasChanged && onFilterChange) {
            onFilterChange(currentFilters);
        }
    }, [selectedCountries, selectedUsers, selectedTags, selectedTypes, onFilterChange]);

    const handleFilterPanelToggle = (pressed: boolean) => {
        setFilterOpen(pressed);
    };

    const handleCountriesChange = (values: string[]) => {
        setSelectedCountries(values);
    };

    const handleUsersChange = (values: string[]) => {
        setSelectedUsers(values);
    };

    const handleTagsChange = (values: string[]) => {
        setSelectedTags(values);
    };

    const handleTypesChange = (values: string[]) => {
        setSelectedTypes(values);
    };

    return (
        <Card>
            <CardHeader className={!filterOpen ? "gap-0" : ""}>
                <div className="flex gap-2">
                    <IconToggle
                        onIcon={<ChevronUp />}
                        offIcon={<ChevronDown />}
                        onToggleChange={handleFilterPanelToggle}
                        aria-label="Toggle filter panel"
                    />
                    <span className="flex items-center gap-2 font-semibold">
                        <Funnel size={16}/> Filters
                    </span>
                </div>
            </CardHeader>
            {filterOpen &&
                <CardContent>
                    <div className="grid md:grid-cols-4 gap-4">
                        <MultiSelect
                            maxSelected={6}
                            label="Select countries"
                            placeholder="Select countries"
                            options={countries}
                            value={selectedCountries}
                            onChange={handleCountriesChange}
                        />

                        <MultiSelect
                            maxSelected={6}
                            label="Select users"
                            placeholder="Select users"
                            options={users}
                            value={selectedUsers}
                            onChange={handleUsersChange}
                        />

                        <MultiSelect
                            maxSelected={6}
                            label="Select tags"
                            placeholder="Select tags"
                            options={tags}
                            value={selectedTags}
                            onChange={handleTagsChange}
                        />

                        <MultiSelect
                            maxSelected={6}
                            label="Select types"
                            placeholder="Select types"
                            options={[
                                { value: "image", label: "Image" },
                                { value: "video", label: "Video" },
                            ]}
                            value={selectedTypes}
                            onChange={handleTypesChange}
                        />
                    </div>
                </CardContent>
            }
        </Card>
    );
};

export default FilterPanel;
