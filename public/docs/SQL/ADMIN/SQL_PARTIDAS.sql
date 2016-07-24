select	p.partida_id,
			p.partida_serie,
			p.partida_rodada,
			p.partida_data,
			t1.time_nome,
			t2.time_nome
from		partida p
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
order by p.partida_serie asc,
			p.partida_data desc