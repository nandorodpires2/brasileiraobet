select	t1.time_nome,
			t2.time_nome,
			p.partida_rodada,
			p.partida_valor,
			count(*) as apostas,		
			(((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio1 / 100)) * p.partida_coringa_valor as premio_1
			-- (((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio2 / 100)) * p.partida_coringa_valor as premio_2,
			-- (((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio3 / 100)) * p.partida_coringa_valor as premio_3			
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
-- where		p.partida_realizada = 0
group by p.partida_id
order by ((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio1 / 100) desc