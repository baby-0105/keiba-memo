import React, { useEffect, useState } from 'react';
import axios from 'axios';

export default function OsakaHaiRaceMemo() {
    const [horses, setHorses] = useState([]);
    const [selectedMemo, setSelectedMemo] = useState(null);
    const [modalOpen, setModalOpen] = useState(false);
    const [memoContent, setMemoContent] = useState('');

    useEffect(() => {
        axios.get('/api/race-memos?race=osaka-hai')
            .then(res => setHorses(res.data))
            .catch(err => console.error(err));
    }, []);

    const openMemoModal = (horseId, race) => {
        setSelectedMemo({ horseId, race });
        setMemoContent(race.memo || '');
        setModalOpen(true);
    };

    const saveMemo = () => {
        axios.post('/api/race-memos', {
            horse_id: selectedMemo.horseId,
            race_id: selectedMemo.race.id,
            memo: memoContent,
        }).then(() => {
            setModalOpen(false);
            axios.get('/api/race-memos?race=osaka-hai').then(res => setHorses(res.data));
        });
    };

    return (
        <div className="p-4">
            <h1 className="text-2xl font-bold mb-4">大阪杯 - 出走馬メモ一覧</h1>
            <div className="overflow-x-auto">
                <table className="table-auto w-full border-collapse">
                    <thead>
                        <tr className="bg-gray-200">
                            <th className="border border-gray-400 px-4 py-3 text-left">馬名</th>
                            <th className="border border-gray-400 px-4 py-3 text-left">今回</th>
                            {['前走', '2走前', '3走前', '4走前', '5走前'].map((label, i) => (
                                <th key={i} className="border border-gray-400 px-4 py-3 text-left">{label}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {horses.map((horse) => (
                            <tr key={horse.id}>
                                <td className="border border-gray-400 px-4 py-2 font-bold bg-gray-100 whitespace-nowrap">{horse.name}</td>

                                {/* 今回（大阪杯） - race name 表示なし */}
                                <td className="border border-gray-300 px-4 py-2 align-top">
                                    {horse.past_races[0] && (
                                        <div className="flex items-center gap-2">
                                            <span>{horse.past_races[0].memo || '...'}</span>
                                            <button
                                                onClick={() => openMemoModal(horse.id, horse.past_races[0])}
                                                className="border rounded px-2 py-1 text-xs"
                                            >
                                                ✏️
                                            </button>
                                        </div>
                                    )}
                                </td>

                                {/* 前走〜5走前 */}
                                {horse.past_races.slice(1).map((race, i) => (
                                    <td key={i} className="border border-gray-300 px-4 py-2 align-top">
                                        <div className="flex flex-col gap-1 text-sm">
                                            <div className="flex items-center gap-2">
                                                <span>{race.memo || '...'}</span>
                                                <button
                                                    onClick={() => openMemoModal(horse.id, race)}
                                                    className="border rounded px-2 py-1 text-xs"
                                                >
                                                    ✏️
                                                </button>
                                            </div>
                                            <div className="text-gray-500 text-xs">{race.name}</div>
                                        </div>
                                    </td>
                                ))}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {modalOpen && (
                <div className="fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
                    <div className="bg-white p-4 rounded w-96">
                        <h2 className="text-lg font-bold mb-2">メモを編集</h2>
                        <textarea
                            value={memoContent}
                            onChange={(e) => setMemoContent(e.target.value)}
                            className="w-full h-32 border rounded p-2"
                        />
                        <div className="mt-4 flex justify-end">
                            <button onClick={saveMemo} className="bg-blue-500 text-white px-4 py-2 rounded">
                                保存
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
