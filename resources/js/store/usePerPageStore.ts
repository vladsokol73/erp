import { create } from "zustand";
import { persist } from "zustand/middleware";

interface PerPageState {
    perPageMap: Record<string, number>;
    setPerPage: (pageKey: string, value: number) => void;
    getPerPage: (pageKey: string, fallback?: number) => number;
}

export const usePerPageStore = create<PerPageState>()(
    persist(
        (set, get) => ({
            perPageMap: {},

            setPerPage: (pageKey, value) => {
                set((state) => ({
                    perPageMap: {
                        ...state.perPageMap,
                        [pageKey]: value,
                    },
                }));
            },

            getPerPage: (pageKey, fallback = 16) => {
                return get().perPageMap[pageKey] ?? fallback;
            },
        }),
        {
            name: "per-page-settings",
            partialize: (state) => ({
                perPageMap: state.perPageMap,
            }),
        }
    )
);
