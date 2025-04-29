<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $raceName }} - 出走馬メモ一覧</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container {
            overflow-x: auto;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .memo {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .race-name {
            color: #666;
            font-size: 0.85rem;
        }

        @media (max-width: 1024px) {
            table {
                width: 250%;
            }
        }
    </style>
</head>
<body>
    <h1 class="text-2xl font-bold mb-4">{{ $raceName }} - 出走馬メモ一覧</h1>

    <div class="table-container">
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>馬名</th>
                    <th>今回</th>
                    <th>前走</th>
                    <th>2走前</th>
                    <th>3走前</th>
                    <th>4走前</th>
                    <th>5走前</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($postRacesByHorses as $horse)
                    <tr>
                        <td>
                            <div class="font-bold">{{ $horse['name'] }}</div>
                            <div class="text-sm text-gray-600">
                                <a href="#" onclick="openHorseMemoModal({{ $horse['id'] }}, '{{ $horse['horse_memo'] ?? '' }}')">
                                    {{ $horse['horse_memo'] ?? '...' }} ✏️
                                </a>
                            </div>
                        </td>
                        @for ($i = 0; $i <= 5; $i++)
                            <td>
                                @if (isset($horse['past_races'][$i]))
                                    <div style="font-size: 12px; color: gray">{{ $i !== 0 ? $horse['past_races'][$i]->name : '' }}</div>
                                    <span>
                                        @if ($horse['is_confirmed'] ?? true)
                                            <a href="#" onclick="openModal({{ $horse['id'] }}, {{ $horse['past_races'][$i]->id }}, '{{ $horse['past_races'][$i]->memo ?? '' }}')">
                                                {{ $horse['past_races'][$i]->memo ?? '...' }} ✏️
                                            </a>
                                        @else
                                            <span class="text-gray-400">（未確定）</span>
                                        @endif
                                    </span>
                                @else
                                    <div>---</div>
                                @endif
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="/races">レース一覧に戻る</a>

    <!-- 🧩 馬メモ編集モーダル -->
    <div id="horseMemoModal" style="display:none; position:fixed; top:25%; left:50%; transform:translateX(-50%); background:white; padding:20px; border:1px solid #ccc; z-index:1000;">
        <h3>馬メモ編集</h3>
        <form method="POST" action="{{ route('horse.memo.update') }}">
            @csrf
            <input type="hidden" name="horse_id" id="horseMemoHorseId">
            <textarea name="memo" id="horseMemoContent" cols="40" rows="5"></textarea>
            <div style="margin-top: 10px;">
                <button type="submit">保存</button>
                <button type="button" onclick="document.getElementById('horseMemoModal').style.display = 'none'">キャンセル</button>
            </div>
        </form>
    </div>

    <!-- ✅ モーダル部分 -->
    <div id="memoModal" style="display:none; position:fixed; top:20%; left:50%; transform:translateX(-50%); background:white; padding:20px; border:1px solid #ccc; z-index:1000;">
        <h3>メモ編集</h3>
        <form id="memoForm" method="POST" action="{{ route('memo.update') }}">
            @csrf
            <input type="hidden" name="horse_id" id="modalHorseId">
            <input type="hidden" name="race_id" id="modalRaceId">
            <textarea name="memo" id="modalMemo" cols="40" rows="5"></textarea>
            <div style="margin-top: 10px;">
                <button type="submit">保存</button>
                <button type="button" onclick="closeModal()">キャンセル</button>
            </div>
        </form>
    </div>
</body>
</html>

<script>
    function openModal(horseId, raceId, memo) {
        document.getElementById('modalHorseId').value = horseId;
        document.getElementById('modalRaceId').value = raceId;
        document.getElementById('modalMemo').value = memo;
        document.getElementById('memoModal').style.display = 'block';
    }

    function openHorseMemoModal(horseId, memo) {
        document.getElementById('horseMemoHorseId').value = horseId;
        document.getElementById('horseMemoContent').value = memo;
        document.getElementById('horseMemoModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('memoModal').style.display = 'none';
    }
</script>
