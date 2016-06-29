/*MONTANTE APOSTA POR RODADA */
select	p.partida_rodada,
			sum(p.partida_valor) as montante
from		aposta a 
			inner join partida p on a.partida_id = p.partida_id
group by p.partida_rodada
order by p.partida_rodada desc
