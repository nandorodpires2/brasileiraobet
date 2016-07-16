/*MONTANTE APOSTA POR RODADA */
select	p.partida_rodada,
			((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio1 / 100) as premio_1,
			((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio2 / 100) as premio_2,
			((p.partida_valor * p.partida_fator_inicial) + sum(p.partida_valor)) * (p.partida_perc_premio3 / 100) as premio_3	
from		aposta a 
			inner join partida p on a.partida_id = p.partida_id
group by p.partida_rodada
order by p.partida_rodada desc
